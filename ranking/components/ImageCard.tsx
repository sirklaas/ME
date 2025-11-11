import React, { useState, useEffect } from 'react';
import { AppState, ImageItem, ResultItem } from '../types';

interface ImageCardProps {
  image: ImageItem;
  result: ResultItem | undefined;
  state: AppState;
  isEliminated: boolean;
  isWinner: boolean;
}

const ImageCard: React.FC<ImageCardProps> = ({ image, result, state, isEliminated, isWinner }) => {
  const [displayPercentage, setDisplayPercentage] = useState(0);
  const [barHeight, setBarHeight] = useState('0%');

  useEffect(() => {
    if (state === 'SHOWING_RESULTS' && result) {
      const targetPercentage = result.percentage;
      
      // Animate bar height
      const barTimeout = setTimeout(() => {
        setBarHeight(`${targetPercentage}%`);
      }, 100);

      // Animate percentage counter
      if (targetPercentage > 0) {
        const duration = 2500; // ms for suspense
        const stepTime = 50; // update every 50ms
        const totalSteps = duration / stepTime;
        const increment = targetPercentage / totalSteps;

        let currentPercentage = 0;
        const timer = setInterval(() => {
          currentPercentage += increment;
          if (currentPercentage >= targetPercentage) {
            clearInterval(timer);
            setDisplayPercentage(targetPercentage);
          } else {
            setDisplayPercentage(Math.floor(currentPercentage));
          }
        }, stepTime);
        
        return () => {
            clearTimeout(barTimeout);
            clearInterval(timer);
        }
      } else {
        setDisplayPercentage(0);
      }
      
      return () => clearTimeout(barTimeout);

    } else if (state === 'IDLE' || state === 'VOTING') {
      // Reset animations
      setDisplayPercentage(0);
      setBarHeight('0%');
    }
  }, [state, result]);

  const WinnerIcon = () => (
    <svg xmlns="http://www.w3.org/2000/svg" className="h-16 w-16 md:h-24 md:w-24 text-amber-300 drop-shadow-lg" viewBox="0 0 20 20" fill="currentColor">
      <path d="M10 2a.75.75 0 01.75.75v3.438l1.98-1.98a.75.75 0 111.06 1.06l-1.98 1.98h3.438a.75.75 0 110 1.5H13.81l1.98 1.98a.75.75 0 11-1.06 1.06l-1.98-1.98v3.438a.75.75 0 11-1.5 0V13.81l-1.98 1.98a.75.75 0 11-1.06-1.06l1.98-1.98H6.19l-1.98 1.98a.75.75 0 11-1.06-1.06l1.98-1.98H2.75a.75.75 0 110-1.5h3.438l-1.98-1.98a.75.75 0 011.06-1.06l1.98 1.98V2.75A.75.75 0 0110 2z" />
    </svg>
  );

  return (
    <div className={`relative aspect-video bg-gray-800 rounded-lg overflow-hidden shadow-2xl flex items-end transition-all duration-500 ${isWinner ? 'ring-4 ring-amber-400 shadow-amber-400/40' : ''}`}>
      {/* Result Bar */}
      <div 
        className="absolute left-0 bottom-0 w-full bg-gradient-to-t from-emerald-500/80 to-emerald-400/70 transition-all ease-out"
        style={{ height: barHeight, transitionDuration: '2500ms' }}
      />

      {/* Image */}
      <img src={image.url} alt={image.title || `Option ${image.id}`} className={`w-full h-full object-cover transition-all duration-500 ${isEliminated ? 'grayscale' : ''}`} />
      
      {/* Black overlay for eliminated or winner states */}
      <div className={`absolute inset-0 bg-black/70 transition-opacity duration-500 ${(isEliminated || isWinner) ? 'opacity-100' : 'opacity-0'}`} />

      {/* Title Overlay */}
      <div className="absolute bottom-0 left-0 w-full p-4 bg-gradient-to-t from-black/80 to-transparent">
        <h3 className="text-xl md:text-2xl font-bold text-white drop-shadow-md">{image.title}</h3>
      </div>

      {/* Percentage Overlay */}
      <div className={`absolute inset-0 flex items-center justify-center transition-opacity duration-500 ${(state === 'SHOWING_RESULTS' && !isWinner && !isEliminated) ? 'opacity-100' : 'opacity-0'}`}>
        <span className="text-6xl md:text-7xl lg:text-8xl font-black text-white" style={{ textShadow: '0 0 15px rgba(0,0,0,0.7)' }}>
          {displayPercentage}%
        </span>
      </div>

       {/* Eliminated/Winner Overlay */}
      <div className={`absolute inset-0 flex flex-col items-center justify-center transition-opacity duration-500 ${(isEliminated || isWinner) ? 'opacity-100' : 'opacity-0'}`}>
        {isWinner && (
          <>
            <WinnerIcon />
            <span className="text-5xl md:text-6xl font-black text-amber-300 uppercase tracking-widest" style={{ textShadow: '0 0 20px black' }}>
              Winner
            </span>
          </>
        )}
        {isEliminated && (
          <span className="text-5xl md:text-6xl font-black text-red-500 transform -rotate-12" style={{ textShadow: '0 0 15px black' }}>
            Eliminated
          </span>
        )}
      </div>
    </div>
  );
};

export default ImageCard;
