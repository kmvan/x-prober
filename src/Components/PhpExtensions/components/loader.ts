import type { CardProps } from '@/Components/Card/components/typings.ts';
import { gettext } from '@/Components/Language/index.ts';
import { PhpExtensionsConstants } from './constants.ts';
import { PhpExtensions as component } from './index.tsx';
import { PhpExtensionsNav as nav } from './nav.tsx';
export const PhpExtensionsLoader = (): CardProps => ({
  id: PhpExtensionsConstants.id,
  title: gettext('PHP Extensions'),
  priority: 500,
  component,
  nav,
});
