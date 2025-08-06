export interface NetworkStatsItemProps {
  id: string;
  rx: number;
  tx: number;
}
export interface NetworkStatsPollDataProps {
  networks: NetworkStatsItemProps[];
  timestamp: number;
}
