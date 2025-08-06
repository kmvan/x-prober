import { observer } from 'mobx-react-lite';
import { type FC, type MouseEvent, useCallback, useState } from 'react';
import { Button } from '@/Components/Button/components/index.tsx';
import { ButtonStatus } from '@/Components/Button/components/typings.ts';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { gettext } from '@/Components/Language/index.ts';
import { OK } from '@/Components/Rest/http-status.ts';
import { ToastStore } from '@/Components/Toast/components/store.ts';
import type { LocationProps } from './typings.ts';
export const Location: FC<{
  ip: string;
}> = observer(({ ip }) => {
  const [loading, setLoading] = useState(false);
  const [location, setLocation] = useState<LocationProps | null>(null);
  const onClick = useCallback(
    async (e: MouseEvent<HTMLButtonElement>) => {
      e.preventDefault();
      e.stopPropagation();
      if (loading) {
        return;
      }
      setLoading(true);
      const { data, status } = await serverFetch<LocationProps>(
        `locationIpv4&ip=${ip}`
      );
      setLoading(false);
      if (data && status === OK) {
        setLocation(data);
        return;
      }
      ToastStore.open(gettext('Can not fetch location.'));
    },
    [ip, loading]
  );
  return (
    <Button
      onClick={onClick}
      status={loading ? ButtonStatus.Loading : ButtonStatus.Pointer}
    >
      {location
        ? Object.values(location).filter(Boolean).join(', ')
        : gettext('Click to fetch')}
    </Button>
  );
});
