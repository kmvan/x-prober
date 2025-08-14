import { observer } from 'mobx-react-lite';
import { type FC, useEffect } from 'react';
import { Link } from '@/Components/Button/components/index.tsx';
import { serverFetch } from '@/Components/Fetch/server-fetch.ts';
import { gettext } from '@/Components/Language/index.ts';
import { OK } from '@/Components/Rest/http-status.ts';
import { template } from '@/Components/Utils/components/template.ts';
import { versionCompare } from '@/Components/Utils/components/version-compare.ts';
import { PhpInfoStore } from './store.ts';
export const PhpInfoPhpVersion: FC = observer(() => {
  const { pollData, latestPhpVersion, setLatestPhpVersion } = PhpInfoStore;
  useEffect(() => {
    const fetchData = async () => {
      const { data, status } = await serverFetch<{ version: string }>(
        'latestPhpVersion'
      );
      if (data?.version && status === OK) {
        setLatestPhpVersion(data.version);
      }
    };
    fetchData();
  }, [setLatestPhpVersion]);
  const phpVersion = pollData?.phpVersion ?? '';
  const compare = versionCompare(phpVersion, latestPhpVersion);
  return (
    <Link
      href="https://www.php.net/"
      title={gettext('Visit PHP.net Official website')}
    >
      {compare === -1
        ? ` ${template(
            gettext('{{oldVersion}} (Latest: {{latestPhpVersion}})'),
            {
              oldVersion: phpVersion,
              latestPhpVersion,
            }
          )}`
        : phpVersion}
    </Link>
  );
});
