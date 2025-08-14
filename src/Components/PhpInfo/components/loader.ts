import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { PhpInfoConstants } from './constants.ts';
import { PhpInfo as content } from './index.tsx';
import { PhpInfoNav as nav } from './nav.tsx';
export const PhpInfoLoader: ModuleProps = {
  id: PhpInfoConstants.id,
  content,
  nav,
};
