
import React from 'react';

interface TimerBarProps {
  timeLeft: number;
  duration: number;
}

const TimerBar: React.FC<TimerBarProps> = ({ timeLeft, duration }) => {
  const percentage = (timeLeft / duration) * 100;

  return (
    <div className="w-full bg-gray-700/50 rounded-full h-4 my-4 overflow-hidden">
      <div
        className="bg-blue-500 h-4 rounded-full transition-all duration-1000 linear"
        style={{
          width: `${percentage}%`,
          boxShadow: '0 0 10px #3b82f6, 0 0 20px #3b82f6' 
        }}
      />
    </div>
  );
};

export default TimerBar;
