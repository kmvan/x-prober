import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { ServerInfoConstants } from './constants.ts';
import { ServerInfo as content } from './index.tsx';
import { ServerInfoNav as nav } from './nav.tsx';export const ServerInfoLoader: ModuleProps = {
  id: ServerInfoConstants.id,
  content,
  nav,
};
