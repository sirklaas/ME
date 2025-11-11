import React, { useMemo } from 'react';

interface VoterDotsProps {
  totalVoters: number;
  votesReceived: number;
}

const VoterDots: React.FC<VoterDotsProps> = ({ totalVoters, votesReceived }) => {
  const dots = useMemo(() => {
    return Array.from({ length: totalVoters }).map((_, i) => ({
      id: i,
      style: {
        top: `${Math.random() * 95 + 2.5}%`, // Avoid edges
        left: `${Math.random() * 95 + 2.5}%`,
        animationDelay: `${Math.random() * 4}s, ${Math.random() * 4}s`, // Staggered delay for fade-in and float
        animationDuration: '1s, 6s',
      },
    }));
  }, [totalVoters]);

  return (
    <div className="absolute inset-0 overflow-hidden pointer-events-none z-0">
      {dots.map((dot, index) => (
        <div
          key={dot.id}
          className={`
            absolute w-2 h-2 bg-blue-400 rounded-full
            shadow-[0_0_8px_theme(colors.blue.500)]
            transition-all duration-500 ease-in-out
            animate-fade-in animate-float
            ${index < votesReceived ? 'opacity-0 scale-0' : 'opacity-70'}
          `}
          style={dot.style}
        />
      ))}
    </div>
  );
};

export default React.memo(VoterDots);