import { type FC, memo } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { ModuleItem } from '@/Components/Module/components/item.tsx';
import { PingConstants } from './constants.ts';
import { PingServerToBrowser } from './server-browser.tsx';
export const Ping: FC = memo(() => {
  return (
    <ModuleItem id={PingConstants.id} title={gettext('Ping')}>
      <PingServerToBrowser />
    </ModuleItem>
  );
});
