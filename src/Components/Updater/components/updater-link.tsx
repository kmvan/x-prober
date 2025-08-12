import { observer } from 'mobx-react-lite';
import { type FC, type MouseEvent, useCallback } from 'react';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { HeaderButton } from '@/Components/Header/components/link.tsx';
import { gettext } from '@/Components/Language/index.ts';
import {
  CREATED,
  FORBIDDEN,
  INSUFFICIENT_STORAGE,
  INTERNAL_SERVER_ERROR,
} from '@/Components/Rest/http-status.ts';
import { ToastStore } from '@/Components/Toast/components/store.ts';
import { UpdaterStore } from './store.ts';
export const UpdaterLink: FC = observer(() => {
  const { setIsUpdating, setIsUpdateError, notiText } = UpdaterStore;
  const { open } = ToastStore;
  const handleUpdate = useCallback(
    async (e: MouseEvent<HTMLButtonElement>) => {
      e.preventDefault();
      e.stopPropagation();
      setIsUpdating(true);
      const { status } = await serverFetch('update');
      switch (status) {
        case CREATED:
          open(gettext('Update success, refreshing...'));
          window.location.reload();
          return;
        case FORBIDDEN:
          open(gettext('Update is disabled in dev mode.'));
          setIsUpdating(false);
          setIsUpdateError(true);
          return;
        case INSUFFICIENT_STORAGE:
        case INTERNAL_SERVER_ERROR:
          open(
            gettext(
              'Can not update file, please check the server permissions and space.'
            )
          );
          setIsUpdating(false);
          setIsUpdateError(true);
          return;
        default:
      }
      open(gettext('Network error, please try again later.'));
      setIsUpdating(false);
      setIsUpdateError(true);
    },
    [setIsUpdating, setIsUpdateError, open]
  );
  return (
    <HeaderButton onClick={handleUpdate} title={gettext('Click to update')}>
      {notiText}
    </HeaderButton>
  );
});
