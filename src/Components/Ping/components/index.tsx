import { type FC, memo } from 'react';
import { CardItem } from '@/Components/Card/components/item.tsx';
import { gettext } from '@/Components/Language/index.ts';
import { PingConstants } from './constants.ts';
import { PingServerToBrowser } from './server-browser.tsx';
export const Ping: FC = memo(() => {
  return (
    <CardItem id={PingConstants.id} title={gettext('Ping')}>
      <PingServerToBrowser />
    </CardItem>
  );
});
