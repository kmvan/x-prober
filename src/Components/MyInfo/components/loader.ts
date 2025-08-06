import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { MyInfoConstants } from './constants.ts';
import { MyInfo as component } from './index.tsx';
import { MyInfoNav as nav } from './nav';
export const MyInfoLoader = (): CardProps => {
  return {
    id: MyInfoConstants.id,
    title: gettext('My Information'),
    priority: 900,
    component,
    nav,
  };
};
