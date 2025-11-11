import React, { useState, useEffect, useCallback, useRef } from 'react';
import { AppState, ResultItem, ImageItem, LocalStorageState } from '../types';
import { DEFAULT_IMAGES, VOTING_DURATION } from '../constants';
import ImageCard from './ImageCard';
import TimerBar from './TimerBar';
import ControlButton from './ControlButton';
import VoterDots from './VoterDots';
import { getVoteCounts, getTotalVotes, generateSessionId } from '../pocketbase';

// --- Helper Functions ---
interface StoredConfig {
  images: ImageItem[];
  players: string[];
}

const getStoredConfig = (): StoredConfig => {
  try {
    const storedConfig = localStorage.getItem('votingSetupData');
    if (storedConfig) {
      const parsed = JSON.parse(storedConfig);
      const images = parsed.images && parsed.images.length === 4 ? parsed.images : DEFAULT_IMAGES;
      const players = Array.isArray(parsed.players) ? parsed.players : [];
      return { images, players };
    }
  } catch (error) { console.error("Failed to parse config", error); }
  return { images: DEFAULT_IMAGES, players: [] };
};


const generateAndDistributePercentages = (count: number): number[] => {
  if (count <= 0) return [];
  let votes = Array(count).fill(0).map(() => Math.random());
  const totalVotes = votes.reduce((sum, vote) => sum + vote, 0);
  if (totalVotes === 0) { // Handle edge case of all zero votes
      const equalShare = Math.floor(100 / count);
      const remainder = 100 % count;
      const percentages = Array(count).fill(equalShare);
      for (let i = 0; i < remainder; i++) percentages[i]++;
      return percentages;
  }

  let percentages = votes.map(vote => Math.floor((vote / totalVotes) * 100));
  let sum = percentages.reduce((s, p) => s + p, 0);
  let i = 0;
  while (sum < 100) {
    percentages[i % count]++;
    sum++;
    i++;
  }
  return percentages;
};

// --- Component ---
const PresenterView: React.FC = () => {
  const [config, setConfig] = useState<StoredConfig>(getStoredConfig());
  
  // Round and State Management
  const [round, setRound] = useState(1);
  const [appState, setAppState] = useState<AppState>('IDLE');
  const [activeImageIds, setActiveImageIds] = useState<number[]>(config.images.map(i => i.id));
  const [eliminatedImageIds, setEliminatedImageIds] = useState<number[]>([]);
  const [winnerId, setWinnerId] = useState<number | null>(null);

  const [timeLeft, setTimeLeft] = useState(VOTING_DURATION);
  const [results, setResults] = useState<ResultItem[]>([]);


  // --- Sound Effects ---
  const startVoteSound = useRef(new Audio('https://cdn.pixabay.com/audio/2022/03/15/audio_70200a12e3.mp3'));
  const tickSound = useRef(new Audio('https://cdn.pixabay.com/audio/2022/10/12/audio_1520s-99195.mp3'));
  const showResultsSound = useRef(new Audio('https://cdn.pixabay.com/audio/2022/11/17/audio_822234c995.mp3'));

  const playSound = useCallback((audioRef: React.RefObject<HTMLAudioElement>) => {
    if (audioRef.current) {
      audioRef.current.currentTime = 0;
      audioRef.current.play().catch(e => console.error("Error playing audio:", e));
    }
  }, []);

  // --- Effects ---
  useEffect(() => {
    const loadedConfig = getStoredConfig();
    setConfig(loadedConfig);
    setActiveImageIds(loadedConfig.images.map(i => i.id));
    
    // Initialize or get session ID
    let sessionId = localStorage.getItem('votingSessionId');
    if (!sessionId) {
      sessionId = generateSessionId();
      localStorage.setItem('votingSessionId', sessionId);
      console.log('Created new voting session:', sessionId);
    } else {
      console.log('Using existing session:', sessionId);
    }
    
    const handleStorageChange = (event: StorageEvent) => {
      if (event.key === 'votingSetupData') {
        const newConfig = getStoredConfig();
        setConfig(newConfig);
        handleReset(); // Reset if config changes
      }
    };
    window.addEventListener('storage', handleStorageChange);
    return () => window.removeEventListener('storage', handleStorageChange);
  }, []);

  // Sync state to localStorage for voter view
  useEffect(() => {
    try {
      const stateToStore: LocalStorageState = { appState, round, activeImageIds };
      localStorage.setItem('votingState', JSON.stringify(stateToStore));
    } catch (error) { console.error("Could not write to localStorage", error); }
  }, [appState, round, activeImageIds]);

  // Timer logic
  useEffect(() => {
    if (appState !== 'VOTING' || timeLeft <= 0) {
      if (appState === 'VOTING' && timeLeft <= 0) {
        handleShowResults(); // Automatically show results when timer ends
      }
      return;
    }
    if (timeLeft < VOTING_DURATION) playSound(tickSound);
    const timerId = setInterval(() => setTimeLeft(prev => prev - 1), 1000);
    return () => clearInterval(timerId);
  }, [appState, timeLeft, playSound]);

  // --- Handlers ---
  const handleStartVote = useCallback(() => {
    playSound(startVoteSound);
    setAppState('VOTING');
    setTimeLeft(VOTING_DURATION);
    setResults([]);
    setWinnerId(null);
  }, [playSound]);

  const handleShowResults = useCallback(async () => {
    playSound(showResultsSound);
    
    const sessionId = localStorage.getItem('votingSessionId') || 'default_session';
    const activeImages = config.images.filter(img => activeImageIds.includes(img.id));
    
    // Get real vote counts from PocketBase
    const voteCounts = await getVoteCounts(sessionId, round);
    const totalVotes = await getTotalVotes(sessionId, round);
    
    let newResults: ResultItem[];
    
    if (totalVotes > 0) {
      // Calculate percentages from real votes
      newResults = activeImages.map((img) => {
        const voteCount = voteCounts[img.id] || 0;
        const percentage = Math.round((voteCount / totalVotes) * 100);
        return {
          id: img.id,
          percentage: percentage,
        };
      });
      
      // Ensure percentages add up to 100
      const totalPercentage = newResults.reduce((sum, r) => sum + r.percentage, 0);
      if (totalPercentage !== 100 && newResults.length > 0) {
        newResults[0].percentage += (100 - totalPercentage);
      }
    } else {
      // Fallback to random if no votes (for testing)
      console.warn('No votes received, using random percentages');
      const randomPercentages = generateAndDistributePercentages(activeImages.length);
      newResults = activeImages.map((img, index) => ({
        id: img.id,
        percentage: randomPercentages[index],
      }));
    }
    
    setResults(newResults);
    setAppState('SHOWING_RESULTS'); // This triggers the bar/percentage animation

    // Delay the elimination/winner announcement to build suspense
    const suspenseDuration = 2600; // Slightly longer than the 2500ms animation
    setTimeout(() => {
      if (round < 3) {
        // Elimination round: find loser
        const loser = newResults.reduce((min, r) => (r.percentage < min.percentage ? r : min), newResults[0]);
        setEliminatedImageIds(prev => [...prev, loser.id]);
      } else {
        // Final round: find winner
        const winner = newResults.reduce((max, r) => (r.percentage > max.percentage ? r : max), newResults[0]);
        setWinnerId(winner.id);
      }
    }, suspenseDuration);

  }, [playSound, config.images, activeImageIds, round]);

  const handleNextRound = useCallback(() => {
    const nextActiveIds = activeImageIds.filter(id => !eliminatedImageIds.includes(id));
    setActiveImageIds(nextActiveIds);
    setRound(prev => prev + 1);
    setAppState('IDLE');
    setResults([]);
  }, [activeImageIds, eliminatedImageIds]);

  const handleReset = useCallback(() => {
    setAppState('IDLE');
    setResults([]);
    setTimeLeft(VOTING_DURATION);
    setRound(1);
    const newConfig = getStoredConfig();
    setConfig(newConfig);
    setActiveImageIds(newConfig.images.map(i => i.id));
    setEliminatedImageIds([]);
    setWinnerId(null);
  }, []);


  // --- Render Logic ---
  const renderControls = () => {
    if (appState === 'SHOWING_RESULTS') {
      if (winnerId) {
        return <ControlButton onClick={handleReset} className="bg-gray-600 hover:bg-gray-700 focus:ring-gray-500 w-full sm:w-auto"><ResetIcon /><span>Reset Game</span></ControlButton>;
      }
       // Disable next round button until elimination is shown
      const isEliminationDone = (round < 3 && eliminatedImageIds.length === round) || (round === 3 && winnerId);
      return <ControlButton onClick={handleNextRound} disabled={!isEliminationDone} className="bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 w-full sm:w-auto"><NextIcon /><span>Start Round {round + 1}</span></ControlButton>;
    }

    return (
      <>
        <ControlButton onClick={handleStartVote} disabled={appState === 'VOTING'} className="bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 w-full sm:w-auto">
          <VoteIcon />
          <span>{appState === 'VOTING' ? 'Voting...' : `Start Round ${round} Vote`}</span>
        </ControlButton>
        <ControlButton onClick={handleShowResults} disabled={appState !== 'VOTING'} className="bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 w-full sm:w-auto">
          <ResultsIcon />
          <span>Show Results</span>
        </ControlButton>
      </>
    );
  };
  
  const voterUrl = `${window.location.origin}${window.location.pathname}?view=voter`;
  const setupUrl = `${window.location.origin}${window.location.pathname}?view=setup`;
  const totalVoters = config.players.length > 0 ? config.players.length : 150;
  
  // Calculate votes received for the dot animation.
  // This formula is designed to ensure all dots disappear by the time the timer reaches 1 second,
  // providing a clear visual cue that voting is complete before results are shown.
  let votesReceived;
  if (appState === 'IDLE') {
    votesReceived = 0;
  } else if (appState === 'VOTING' && timeLeft > 0) {
    const elapsedTime = VOTING_DURATION - timeLeft;
    // Using VOTING_DURATION - 1 as a divisor makes the progress hit 100% when elapsedTime is 19 (i.e., timeLeft is 1).
    const divisor = Math.max(1, VOTING_DURATION - 1);
    const progress = elapsedTime / divisor;
    votesReceived = Math.floor(totalVoters * progress);
  } else {
    // Handles VOTING with timeLeft <= 0, and SHOWING_RESULTS states.
    votesReceived = totalVoters;
  }

  return (
    <div className="min-h-screen container mx-auto p-4 md:p-8 flex flex-col relative">
      {appState === 'VOTING' && <VoterDots totalVoters={totalVoters} votesReceived={votesReceived} />}
      <header className="text-center mb-4 md:mb-8 z-10">
        <h2 className="text-2xl font-semibold text-blue-300 tracking-widest">
            {winnerId ? "WINNER" : `ROUND ${round}`}
        </h2>
        <h1 className="text-4xl md:text-5xl font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">
          Live Audience Voting
        </h1>
        <div className="mt-4 p-3 bg-gray-800/50 rounded-lg inline-flex flex-col sm:flex-row gap-4 border border-gray-700">
            <a href={voterUrl} target="_blank" rel="noopener noreferrer" className="text-blue-400 hover:text-blue-300">Voter Link</a>
            <span className="text-gray-600 hidden sm:inline">|</span>
            <a href={setupUrl} target="_blank" rel="noopener noreferrer" className="text-emerald-400 hover:text-emerald-300">Setup Page</a>
        </div>
      </header>
      
      <main className="flex-grow grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8 z-10">
        {config.images.map(image => (
          <ImageCard 
            key={image.id} 
            image={image} 
            result={results.find(r => r.id === image.id)} 
            state={appState}
            isEliminated={eliminatedImageIds.includes(image.id)}
            isWinner={winnerId === image.id}
          />
        ))}
      </main>

      <footer className="py-4 md:py-8 mt-4 md:mt-8 z-10">
        <div className="w-full max-w-3xl mx-auto">
          {appState === 'VOTING' && (
            <>
              <div className="flex justify-between items-center text-lg text-blue-300">
                <span>{votesReceived} / {totalVoters} Votes Received</span>
                <span>{Math.floor(timeLeft/60)}:{(timeLeft%60).toString().padStart(2, '0')}</span>
              </div>
              <TimerBar timeLeft={timeLeft} duration={VOTING_DURATION} />
            </>
          )}

          <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
            {renderControls()}
          </div>
        </div>
      </footer>
    </div>
  );
};

// --- Icons ---
const VoteIcon = () => (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>);
const ResultsIcon = () => (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>);
const ResetIcon = () => (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 4v5h5M20 20v-5h-5M4 4l1.5 1.5A9 9 0 0120.5 10M20 20l-1.5-1.5A9 9 0 003.5 14" /></svg>);
const NextIcon = () => (<svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}><path strokeLinecap="round" strokeLinejoin="round" d="M13 5l7 7-7 7M5 5l7 7-7 7" /></svg>);

export default PresenterView;