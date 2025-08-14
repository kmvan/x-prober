import type { ModuleProps } from '@/Components/Module/components/typings.ts';
import { PhpExtensionsConstants } from './constants.ts';
import { PhpExtensions as content } from './index.tsx';
import { PhpExtensionsNav as nav } from './nav.tsx';
export const PhpExtensionsLoader: ModuleProps = {
  id: PhpExtensionsConstants.id,
  content,
  nav,
};
