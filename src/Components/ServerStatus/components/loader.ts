import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { ServerStatusConstants } from './constants.ts';
import { ServerStatus as content } from './index.tsx';
import { ServerStatusNav as nav } from './nav.tsx';
export const ServerStatusLoader: ModuleProps = {
  id: ServerStatusConstants.id,
  content,
  nav,
};
