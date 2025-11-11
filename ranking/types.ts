export type AppState = 'IDLE' | 'VOTING' | 'SHOWING_RESULTS';

export interface ImageItem {
  id: number;
  url: string;
  title: string;
}

export interface ResultItem {
  id: number;
  percentage: number;
}

export interface LocalStorageState {
  appState: AppState;
  round: number;
  activeImageIds: number[];
}
