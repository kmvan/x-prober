import type { FC } from 'react';export interface ModuleProps {
  id: string;
  content: FC;
  nav: FC;
}
export interface SortedModuleProps {
  id: string;
  priority: number;
}
