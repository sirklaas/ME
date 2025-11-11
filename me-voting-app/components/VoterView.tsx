import React, { useState, useEffect } from 'react';
import { AppState, ImageItem, LocalStorageState } from '../types';
import { DEFAULT_IMAGES } from '../constants';
import { submitVote, getVoterFingerprint } from '../pocketbase';

const getStoredConfig = (): { images: ImageItem[] } => {
  try {
    const storedConfig = localStorage.getItem('votingSetupData');
    if (storedConfig) {
      const parsed = JSON.parse(storedConfig);
      if (parsed.images && parsed.images.length === 4) {
        return { images: parsed.images };
      }
    }
  } catch (error) { console.error("Failed to parse config", error); }
  return { images: DEFAULT_IMAGES };
};

const getInitialState = (): LocalStorageState => {
  try {
    const storedState = localStorage.getItem('votingState');
    if (storedState) {
      const parsed = JSON.parse(storedState);
      // Add validation if needed
      return parsed as LocalStorageState;
    }
  } catch (error) { console.error("Failed to parse voting state", error); }
  return { appState: 'IDLE', round: 1, activeImageIds: DEFAULT_IMAGES.map(i => i.id) };
};

const VoterView: React.FC = () => {
  const [votingState, setVotingState] = useState<LocalStorageState>(getInitialState);
  const [votedFor, setVotedFor] = useState<number | null>(null);
  const [config, setConfig] = useState<{ images: ImageItem[] }>({ images: DEFAULT_IMAGES });

  useEffect(() => {
    setConfig(getStoredConfig());

    const handleStorageChange = (event: StorageEvent) => {
      if (event.key === 'votingState' && event.newValue) {
        try {
          const newState = JSON.parse(event.newValue) as LocalStorageState;
          
          // Reset vote if round changes or state is no longer 'VOTING'
          if (newState.round !== votingState.round || newState.appState !== 'VOTING') {
            setVotedFor(null);
          }
          setVotingState(newState);

        } catch (e) { console.error("Error parsing new state", e); }
      }
      if (event.key === 'votingSetupData') {
        setConfig(getStoredConfig());
      }
    };

    window.addEventListener('storage', handleStorageChange);
    return () => window.removeEventListener('storage', handleStorageChange);
  }, [votingState.round]); // Rerun effect if round changes to reset vote correctly

  const handleVote = async (id: number) => {
    if (votingState.appState === 'VOTING' && !votedFor) {
      setVotedFor(id);
      
      // Get session ID from localStorage
      const sessionId = localStorage.getItem('votingSessionId') || 'default_session';
      const voterId = getVoterFingerprint();
      
      // Submit vote to PocketBase
      const success = await submitVote(sessionId, votingState.round, voterId, id);
      
      if (success) {
        console.log(`Vote submitted: image ${id}, round ${votingState.round}`);
      } else {
        console.error('Failed to submit vote to PocketBase');
      }
    }
  };

  const getStatusMessage = () => {
    const { appState } = votingState;
    switch (appState) {
      case 'IDLE':
        return `Round ${votingState.round} will begin shortly...`;
      case 'SHOWING_RESULTS':
        return 'Voting has ended. Please see the main screen for results.';
      case 'VOTING':
        return votedFor ? `Thank you for voting!` : `Round ${votingState.round}: Tap a picture to vote!`;
      default:
        return '';
    }
  };

  const activeImages = config.images.filter(img => votingState.activeImageIds.includes(img.id));

  return (
    <div className="min-h-screen bg-gray-900 text-white flex flex-col items-center justify-center p-4">
      <header className="text-center mb-8">
        <h1 className="text-4xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-emerald-400">
          Cast Your Vote
        </h1>
        <p className={`text-gray-400 mt-2 text-lg ${(votingState.appState === 'VOTING' && !votedFor) ? 'animate-pulse' : ''}`}>
          {getStatusMessage()}
        </p>
      </header>

      <main className={`w-full max-w-xl grid gap-4 ${activeImages.length === 3 ? 'grid-cols-2' : 'grid-cols-2'}`}>
        {activeImages.map((image: ImageItem, index) => (
          <div 
            key={image.id} 
            className={`
              flex flex-col items-center gap-2 
              ${(activeImages.length === 3 && index === 0) ? 'col-span-2' : ''}
            `}
          >
            <button
              onClick={() => handleVote(image.id)}
              disabled={votingState.appState !== 'VOTING' || !!votedFor}
              className={`
                relative aspect-square rounded-lg overflow-hidden w-full
                transform transition-all duration-300
                focus:outline-none focus:ring-4
                ${(votingState.appState === 'VOTING' && !votedFor) ? 'cursor-pointer hover:scale-105 focus:ring-blue-500' : 'cursor-not-allowed'}
                ${(votedFor === image.id) ? 'ring-4 ring-emerald-500 scale-105 shadow-2xl' : 'ring-2 ring-transparent'}
                ${(votingState.appState !== 'VOTING') ? 'opacity-60' : ''}
              `}
            >
              <img src={image.url} alt={image.title || `Option ${image.id}`} className="w-full h-full object-cover" />
              {votedFor === image.id && (
                <div className="absolute inset-0 bg-black/70 flex items-center justify-center">
                   <svg xmlns="http://www.w3.org/2000/svg" className="h-16 w-16 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                      <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                   </svg>
                </div>
              )}
              <div className={`absolute inset-0 bg-gray-900/50 transition-opacity ${(votingState.appState === 'VOTING' || votedFor) ? 'opacity-0' : 'opacity-100'}`} />
            </button>
            <p className="font-semibold text-center">{image.title}</p>
          </div>
        ))}
      </main>
    </div>
  );
};

export default VoterView;
