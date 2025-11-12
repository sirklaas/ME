// PocketBase Configuration for Voting App
// Note: PocketBase SDK loaded via CDN in index.html

// @ts-ignore - PocketBase loaded globally from CDN
const PocketBase = window.PocketBase;

// Initialize PocketBase client
// Using ranking subdomain for dedicated voting app instance
export const pb = new PocketBase('https://ranking.pinkmilk.eu');

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

// Types for session configuration
export interface SessionConfig {
  id?: string;
  session_id: string;
  images: Array<{ id: number; url: string; title: string }>;
  players: string[];
  created?: string;
  updated?: string;
}

// Save session configuration to PocketBase
export async function saveSessionConfig(sessionId: string, images: any[], players: string[]): Promise<boolean> {
  try {
    // Check if config already exists for this session
    const existing = await pb.collection('voting_session').getFirstListItem<SessionConfig>(
      `session_id = "${sessionId}"`
    ).catch(() => null);

    const configData = {
      session_id: sessionId,
      images: images,
      players: players,
    };

    if (existing) {
      // Update existing config
      await pb.collection('voting_session').update(existing.id!, configData);
    } else {
      // Create new config
      await pb.collection('voting_session').create(configData);
    }

    console.log('Configuration saved to PocketBase (voting_session):', configData);
    return true;
  } catch (error) {
    console.error('Failed to save configuration to PocketBase:', error);
    return false;
  }
}

// Load session configuration from PocketBase
export async function loadSessionConfig(sessionId: string): Promise<SessionConfig | null> {
  try {
    const config = await pb.collection('voting_session').getFirstListItem<SessionConfig>(
      `session_id = "${sessionId}"`
    );
    console.log('Configuration loaded from PocketBase (voting_session):', config);
    return config;
  } catch (error) {
    console.log('No configuration found in PocketBase for session:', sessionId);
    return null;
  }
}
