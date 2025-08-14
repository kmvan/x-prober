import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { NodesConstants } from './constants.ts';
import { Nodes as content } from './index.tsx';
import { NodesNav as nav } from './nav.tsx';
export const NodesLoader: ModuleProps = {
  id: NodesConstants.id,
  content,
  nav,
};
