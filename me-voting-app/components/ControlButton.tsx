
import React from 'react';

interface ControlButtonProps {
  onClick: () => void;
  disabled?: boolean;
  children: React.ReactNode;
  className?: string;
}

const ControlButton: React.FC<ControlButtonProps> = ({ onClick, disabled = false, children, className = '' }) => {
  return (
    <button
      onClick={onClick}
      disabled={disabled}
      className={`
        flex items-center justify-center gap-2 px-6 py-3 font-semibold text-white rounded-lg shadow-md
        transition-all duration-300 ease-in-out
        focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-gray-900
        disabled:opacity-50 disabled:cursor-not-allowed
        ${className}
      `}
    >
      {children}
    </button>
  );
};

export default ControlButton;
