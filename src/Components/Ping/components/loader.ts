import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { PingConstants } from './constants.ts';
import { Ping as content } from './index.tsx';
import { PingNav as nav } from './nav.tsx';

export const PingLoader: ModuleProps = {
  id: PingConstants.id,
  content,
  nav,
};
