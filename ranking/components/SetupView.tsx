import React, { useState, useEffect, useCallback } from 'react';
import { ImageItem } from '../types';
import { DEFAULT_IMAGES } from '../constants';
import ControlButton from './ControlButton';
import { generateSessionId, saveSessionConfig, loadSessionConfig, uploadImage } from '../pocketbase';

// Make TypeScript aware of the XLSX library from the CDN
declare const XLSX: any;

const getInitialConfig = (): { players: string[], images: ImageItem[] } => {
  try {
    const stored = localStorage.getItem('votingSetupData');
    if (stored) {
      const parsed = JSON.parse(stored);
      // Basic validation
      if (Array.isArray(parsed.players) && Array.isArray(parsed.images) && parsed.images.length === 4) {
        return parsed;
      }
    }
  } catch (error) {
    console.error("Failed to load initial config:", error);
  }
  return { players: [], images: DEFAULT_IMAGES.map(img => ({ ...img })) };
};


const SetupView: React.FC = () => {
  const [players, setPlayers] = useState<string[]>(getInitialConfig().players);
  const [images, setImages] = useState<ImageItem[]>(getInitialConfig().images);
  const [statusMessage, setStatusMessage] = useState<{ type: 'success' | 'error', text: string } | null>(null);
  const [isLoading, setIsLoading] = useState(true);

  // Load config from PocketBase on mount
  useEffect(() => {
    const loadConfig = async () => {
      const sessionId = localStorage.getItem('votingSessionId') || 'default_session';
      const config = await loadSessionConfig(sessionId);
      
      if (config) {
        console.log('Loaded config from PocketBase:', config);
        if (config.images && Array.isArray(config.images)) {
          setImages(config.images);
        }
        if (config.players && Array.isArray(config.players)) {
          setPlayers(config.players);
        }
        setStatusMessage({ type: 'success', text: 'Configuration loaded from PocketBase' });
      } else {
        console.log('No config found in PocketBase, using defaults');
      }
      setIsLoading(false);
    };
    
    loadConfig();
  }, []);

  const handleImageChange = async (index: number, file: File) => {
    const reader = new FileReader();
    reader.onload = async (e) => {
      const base64Data = e.target?.result as string;
      const newImages = [...images];
      
      // Show base64 preview immediately
      newImages[index].url = base64Data;
      setImages(newImages);
      
      // Upload to server in background
      setStatusMessage({ type: 'success', text: `Uploading image ${index + 1}...` });
      const serverUrl = await uploadImage(base64Data, file.name);
      
      if (serverUrl) {
        // Update with server URL
        newImages[index].url = serverUrl;
        setImages(newImages);
        setStatusMessage({ type: 'success', text: `Image ${index + 1} uploaded successfully!` });
      } else {
        setStatusMessage({ type: 'error', text: `Failed to upload image ${index + 1}. Using local copy.` });
      }
    };
    reader.readAsDataURL(file);
  };

  const handleTitleChange = (index: number, title: string) => {
    const newImages = [...images];
    newImages[index].title = title;
    setImages(newImages);
  };

  const handleExcelImport = (event: React.ChangeEvent<HTMLInputElement>) => {
    const file = event.target.files?.[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
      try {
        const data = new Uint8Array(e.target?.result as ArrayBuffer);
        const workbook = XLSX.read(data, { type: 'array' });
        const sheetName = workbook.SheetNames[0];
        const worksheet = workbook.Sheets[sheetName];
        // Assuming players are in the first column
        const json: any[][] = XLSX.utils.sheet_to_json(worksheet, { header: 1 });
        const playerNames = json.map(row => row[0]).filter(name => typeof name === 'string' && name.trim() !== '');
        setPlayers(playerNames);
        setStatusMessage({ type: 'success', text: `Successfully imported ${playerNames.length} players.` });
      } catch (err) {
        console.error("Error parsing Excel file:", err);
        setStatusMessage({ type: 'error', text: 'Failed to parse the Excel file. Please check the format.' });
      }
    };
    reader.readAsArrayBuffer(file);
  };

  const handleSaveConfiguration = useCallback(async () => {
    try {
      const sessionId = localStorage.getItem('votingSessionId') || 'default_session';
      console.log('Saving configuration to PocketBase for session:', sessionId);
      
      // DON'T save to localStorage - causes quota exceeded with base64 images
      // Only save to PocketBase with server URLs
      
      // For PocketBase, save image metadata with server URLs
      const imageMetadata = images.map(img => ({
        id: img.id,
        title: img.title,
        url: img.url // Now contains server URL or default URL
      }));
      
      // Save to PocketBase
      const success = await saveSessionConfig(sessionId, imageMetadata, players);
      
      if (success) {
        setStatusMessage({ 
          type: 'success', 
          text: `✅ Saved to PocketBase! ${players.length} players, ${images.length} images` 
        });
      } else {
        setStatusMessage({ 
          type: 'error', 
          text: 'Failed to save to PocketBase. Check console for details.' 
        });
      }
    } catch (error) {
      console.error("Failed to save config:", error);
      setStatusMessage({ 
        type: 'error', 
        text: `Error: ${error instanceof Error ? error.message : 'Could not save configuration'}` 
      });
    }
  }, [players, images]);

  const handleNewSession = useCallback(() => {
    const newSessionId = generateSessionId();
    localStorage.setItem('votingSessionId', newSessionId);
    setStatusMessage({ type: 'success', text: `New session created: ${newSessionId}` });
  }, []);

  useEffect(() => {
    if (statusMessage) {
      const timer = setTimeout(() => setStatusMessage(null), 5000);
      return () => clearTimeout(timer);
    }
  }, [statusMessage]);

  const presenterUrl = `${window.location.origin}${window.location.pathname}`;
  const voterUrl = `${window.location.origin}${window.location.pathname}?view=voter`;

  return (
    <div className="min-h-screen container mx-auto p-4 md:p-8">
      <header className="text-center mb-8">
        <h1 className="text-4xl md:text-5xl font-bold tracking-tight text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-400">
          Voting Setup Panel
        </h1>
        <p className="text-gray-400 mt-2">Configure your voting session here. Changes are saved to this browser.</p>
        <div className="mt-4 p-3 bg-gray-800/50 rounded-lg inline-flex flex-col sm:flex-row gap-4 border border-gray-700">
          <div>
            <p className="text-gray-300 font-medium">Presenter View:</p>
            <a href={presenterUrl} target="_blank" rel="noopener noreferrer" className="text-blue-400 hover:text-blue-300 break-all">{presenterUrl}</a>
          </div>
          <div className="border-l border-gray-600 pl-4">
            <p className="text-gray-300 font-medium">Voter Link:</p>
            <a href={voterUrl} target="_blank" rel="noopener noreferrer" className="text-blue-400 hover:text-blue-300 break-all">{voterUrl}</a>
          </div>
        </div>
      </header>

      <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {/* Player Import Section */}
        <div className="lg:col-span-1 bg-gray-800/50 p-6 rounded-lg border border-gray-700">
          <h2 className="text-2xl font-bold mb-4 text-emerald-400">Import Players</h2>
          <p className="text-gray-400 mb-4">Upload an Excel file (.xlsx) with player names in the first column.</p>
          <input
            type="file"
            accept=".xlsx, .xls"
            onChange={handleExcelImport}
            className="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700"
          />
          <div className="mt-4 bg-gray-900 rounded-lg p-3 h-64 overflow-y-auto border border-gray-700">
            <h3 className="font-semibold">{players.length} Players Imported</h3>
            <ul className="list-disc list-inside mt-2 text-gray-300">
              {players.map((player, index) => <li key={index}>{player}</li>)}
            </ul>
          </div>
        </div>

        {/* Image & Title Setup Section */}
        <div className="lg:col-span-2 bg-gray-800/50 p-6 rounded-lg border border-gray-700">
          <h2 className="text-2xl font-bold mb-4 text-emerald-400">Configure Voting Options</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {images.map((image, index) => (
              <div key={image.id} className="space-y-3">
                <img src={image.url} alt={`Preview ${index + 1}`} className="aspect-video w-full rounded-md object-cover bg-gray-700" />
                <input
                  type="text"
                  placeholder={`Title for Option ${index + 1}`}
                  value={image.title}
                  onChange={(e) => handleTitleChange(index, e.target.value)}
                  className="w-full bg-gray-700 text-white px-3 py-2 rounded-md border border-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                />
                <input
                  type="file"
                  accept="image/*"
                  onChange={(e) => e.target.files && handleImageChange(index, e.target.files[0])}
                  className="block w-full text-sm text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700"
                />
              </div>
            ))}
          </div>
        </div>
      </div>

      <footer className="mt-8 text-center space-y-4">
        <div className="flex flex-col sm:flex-row gap-4 justify-center">
          <ControlButton onClick={handleSaveConfiguration} className="bg-emerald-600 hover:bg-emerald-700 focus:ring-emerald-500 w-full sm:w-auto">
            Save Configuration
          </ControlButton>
          <ControlButton onClick={handleNewSession} className="bg-blue-600 hover:bg-blue-700 focus:ring-blue-500 w-full sm:w-auto">
            Create New Session
          </ControlButton>
        </div>
        {statusMessage && (
          <p className={`mt-4 text-lg ${statusMessage.type === 'success' ? 'text-green-400' : 'text-red-400'}`}>
            {statusMessage.text}
          </p>
        )}
      </footer>
    </div>
  );
};

export default SetupView;
