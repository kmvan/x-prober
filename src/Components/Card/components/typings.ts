import type { FC } from 'react';
export interface CardProps {
  id: string;
  title: string;
  enabled?: boolean;
  priority: number;
  component: FC;
  nav: FC;
}
