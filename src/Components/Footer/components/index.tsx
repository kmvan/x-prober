import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { BootstrapStore } from '@/Components/Bootstrap/components/store.ts';
import { gettext } from '@/Components/Language/index.ts';
import { template } from '@/Components/Utils/components/template';
import styles from './index.module.scss';
export const Footer: FC = observer(() => {
  const { pollData } = BootstrapStore;
  if (!pollData) {
    return null;
  }
  const { appName, appUrl, authorName, authorUrl } = pollData;
  return (
    <div
      className={styles.main}
      dangerouslySetInnerHTML={{
        __html: template(
          gettext('Generator {{appName}} / Author {{authorName}}'),
          {
            appName: `<a href="${appUrl}" target="_blank">${appName}</a>`,
            authorName: `<a href="${authorUrl}" target="_blank">${authorName}</a>`,
          }
        ),
      }}
    />
  );
});
