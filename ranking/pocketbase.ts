// PocketBase Configuration for Voting App
// Note: PocketBase SDK loaded via CDN in index.html

// @ts-ignore - PocketBase loaded globally from CDN
const PocketBase = window.PocketBase;

// Initialize PocketBase client
// Using PocketHost instance
export const pb = new PocketBase('https://pinkmilk.pockethost.io');

// Disable auto-cancellation for better reliability
pb.autoCancellation(false);

// Types for our votes collection
export interface Vote {
  id?: string;
  session_id: string;
  round: number;
  voter_id: string;
  image_id: number;
  created?: string;
}

// Vote submission function
export async function submitVote(sessionId: string, round: number, voterId: string, imageId: number): Promise<boolean> {
  try {
    await pb.collection('votes').create({
      session_id: sessionId,
      round: round,
      voter_id: voterId,
      image_id: imageId,
    });
    return true;
  } catch (error) {
    console.error('Failed to submit vote:', error);
    return false;
  }
}

// Get vote counts for a specific session and round
export async function getVoteCounts(sessionId: string, round: number): Promise<Record<number, number>> {
  try {
    const votes = await pb.collection('votes').getFullList<Vote>({
      filter: `session_id = "${sessionId}" && round = ${round}`,
    });

    // Count votes per image
    const counts: Record<number, number> = {};
    votes.forEach(vote => {
      counts[vote.image_id] = (counts[vote.image_id] || 0) + 1;
    });

    return counts;
  } catch (error) {
    console.error('Failed to get vote counts:', error);
    return {};
  }
}

// Check if voter has already voted in this round
export async function hasVoted(sessionId: string, round: number, voterId: string): Promise<boolean> {
  try {
    const result = await pb.collection('votes').getFirstListItem<Vote>(
      `session_id = "${sessionId}" && round = ${round} && voter_id = "${voterId}"`
    );
    return !!result;
  } catch (error) {
    // If no record found, user hasn't voted
    return false;
  }
}

// Get total vote count for a session/round
export async function getTotalVotes(sessionId: string, round: number): Promise<number> {
  try {
    const votes = await pb.collection('votes').getFullList<Vote>({
      filter: `session_id = "${sessionId}" && round = ${round}`,
    });
    return votes.length;
  } catch (error) {
    console.error('Failed to get total votes:', error);
    return 0;
  }
}

// Generate a unique device fingerprint for voter identification
export function getVoterFingerprint(): string {
  // Check if we already have a fingerprint stored
  let fingerprint = localStorage.getItem('voter_fingerprint');
  
  if (!fingerprint) {
    // Create a simple fingerprint from browser data
    const data = [
      navigator.userAgent,
      navigator.language,
      screen.width,
      screen.height,
      new Date().getTimezoneOffset(),
      Math.random().toString(36).substring(2, 15), // Random component
    ].join('|');
    
    // Simple hash function
    fingerprint = btoa(data).substring(0, 50);
    localStorage.setItem('voter_fingerprint', fingerprint);
  }
  
  return fingerprint;
}

// Generate a unique session ID
export function generateSessionId(): string {
  return `session_${Date.now()}_${Math.random().toString(36).substring(2, 9)}`;
}

// Fetch the 4 voting images from server
export async function fetchVoteImages(): Promise<any[] | null> {
  try {
    const response = await fetch('https://www.pinkmilk.eu/ME/get-vote-images.php');
    
    if (!response.ok) {
      throw new Error(`Failed to fetch images: ${response.status}`);
    }
    
    const result = await response.json();
    
    if (result.success && result.images) {
      console.log('Loaded images from server:', result.images);
      return result.images;
    } else {
      console.error('Failed to load images:', result.error);
      return null;
    }
  } catch (error) {
    console.error('Failed to fetch vote images:', error);
    return null;
  }
}

// Types for session configuration
export interface SessionConfig {
  id?: string;
  session_id: string;
  titles: string[]; // Just the 4 titles, not full image objects
  players: string[];
  created?: string;
  updated?: string;
}

// Save session configuration to PocketBase (only titles and players)
export async function saveSessionConfig(sessionId: string, titles: string[], players: string[]): Promise<boolean> {
  try {
    // Check if config already exists for this session
    const existing = await pb.collection('voting_session').getFirstListItem<SessionConfig>(
      `session_id = "${sessionId}"`
    ).catch(() => null);

    const configData = {
      session_id: sessionId,
      titles: JSON.stringify(titles), // Just the 4 titles
      players: JSON.stringify(players),
    };

    console.log('Saving to PocketBase:', configData);

    if (existing) {
      // Update existing config
      await pb.collection('voting_session').update(existing.id!, configData);
      console.log('Updated existing record:', existing.id);
    } else {
      // Create new config
      const result = await pb.collection('voting_session').create(configData);
      console.log('Created new record:', result);
    }

    console.log('Configuration saved to PocketBase');
    return true;
  } catch (error: any) {
    console.error('Failed to save configuration to PocketBase:', error);
    console.error('Error details:', {
      status: error?.status,
      message: error?.message,
      data: error?.data,
      response: error?.response
    });
    return false;
  }
}

// Load session configuration from PocketBase
export async function loadSessionConfig(sessionId: string): Promise<SessionConfig | null> {
  try {
    const config = await pb.collection('voting_session').getFirstListItem<any>(
      `session_id = "${sessionId}"`
    );
    
    // Parse JSON strings back to arrays
    const parsedConfig: SessionConfig = {
      ...config,
      titles: typeof config.titles === 'string' ? JSON.parse(config.titles) : (config.titles || []),
      players: typeof config.players === 'string' ? JSON.parse(config.players) : (config.players || []),
    };
    
    console.log('Configuration loaded from PocketBase:', parsedConfig);
    return parsedConfig;
  } catch (error) {
    console.log('No configuration found in PocketBase for session:', sessionId);
    return null;
  }
}
