import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { PhpInfoConstants } from './constants.ts';
import { PhpInfo as component } from './index.tsx';
import { PhpInfoNav as nav } from './nav.tsx';
export const PhpInfoLoader = (): CardProps => ({
  id: PhpInfoConstants.id,
  title: gettext('PHP Information'),
  priority: 400,
  component,
  nav,
});
