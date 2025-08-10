import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { DatabaseConstants } from './constants.ts';
import { Database as content } from './index.tsx';
import { DatabaseNav as nav } from './nav.tsx';
export const DatabaseLoader: ModuleProps = {
  id: DatabaseConstants.id,
  content,
  nav,
};
