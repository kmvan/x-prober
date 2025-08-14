import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { MyInfoConstants } from './constants.ts';
import { MyInfo as content } from './index.tsx';
import { MyInfoNav as nav } from './nav';
export const MyInfoLoader: ModuleProps = {
  id: MyInfoConstants.id,
  content,
  nav,
};
