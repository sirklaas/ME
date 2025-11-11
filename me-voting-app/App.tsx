import React, { useState, useEffect } from 'react';
import PresenterView from './components/PresenterView';
import VoterView from './components/VoterView';
import SetupView from './components/SetupView';

const App: React.FC = () => {
  const [view, setView] = useState<'presenter' | 'voter' | 'setup' | null>(null);

  useEffect(() => {
    // This ensures we don't have a mismatch between server and client rendering in a real app
    const urlParams = new URLSearchParams(window.location.search);
    const viewParam = urlParams.get('view');
    
    if (viewParam === 'voter') {
      setView('voter');
    } else if (viewParam === 'setup') {
      setView('setup');
    } else {
      setView('presenter');
    }
  }, []);

  if (view === null) {
    // Render a blank screen or a spinner to avoid a flash of incorrect content
    return <div className="min-h-screen bg-gray-900" />;
  }
  
  switch (view) {
    case 'voter':
      return <VoterView />;
    case 'setup':
      return <SetupView />;
    case 'presenter':
    default:
      return <PresenterView />;
  }
};

export default App;
