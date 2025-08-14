import type { PollDataProps } from '@/Components/Poll/components/typings.ts';
export interface NodesItemProps {
  id: string;
  loading: boolean;
  status: number;
  data: PollDataProps | null;
}
export interface NodesPollDataProps {
  nodesIds: string[];
}
