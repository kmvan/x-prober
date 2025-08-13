import { observer } from 'mobx-react-lite';
import { type FC, useEffect } from 'react';
import { ConfigStore } from '@/Components/Config/store.ts';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { OK } from '@/Components/Rest/http-status.ts';
import { UpdaterStore } from '@/Components/Updater/components/store';
import { UpdaterLink } from '@/Components/Updater/components/updater-link';
import { versionCompare } from '@/Components/Utils/components/version-compare.ts';
import { HeaderLink } from './link.tsx';
import styles from './name.module.scss';
export const HeaderName: FC = observer(() => {
  const { pollData } = ConfigStore;
  const { setTargetVersion, targetVersion } = UpdaterStore;
  // fetch new version
  useEffect(() => {
    if (!pollData) {
      return;
    }
    const fetchData = async () => {
      const { data, status } = await serverFetch<{
        version: string;
      }>('latestVersion');
      if (!data?.version || status !== OK) {
        return;
      }
      setTargetVersion(data.version);
    };
    fetchData();
  }, [pollData, setTargetVersion]);
  if (!pollData) {
    return null;
  }
  const { APP_NAME, APP_URL, APP_VERSION } = pollData;
  return (
    <h1 className={styles.main}>
      {targetVersion && versionCompare(targetVersion, APP_VERSION) < 0 ? (
        <UpdaterLink />
      ) : (
        <HeaderLink href={APP_URL} rel="noreferrer" target="_blank">
          <span className={styles.name}>{APP_NAME}</span>
          <span className={styles.version}>{APP_VERSION}</span>
        </HeaderLink>
      )}
    </h1>
  );
});
