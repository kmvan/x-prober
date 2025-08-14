export interface ServerBenchmarkMarksProps {
  cpu: number;
  read: number;
  write: number;
}
export interface ServerBenchmarkProps {
  id: string;
  name: string;
  url: string;
  date: string;
  probeUrl: string;
  binUrl: string;
  total: number;
  detail: ServerBenchmarkMarksProps;
}
