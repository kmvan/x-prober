import { observer } from 'mobx-react-lite';
import type { FC } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { PollStore } from '@/Components/Poll/components/store.ts';
import { template } from '@/Components/Utils/components/template';
import styles from './index.module.scss';
export const Footer: FC = observer(() => {
  const { pollData } = PollStore;
  if (!pollData?.config) {
    return null;
  }
  const { APP_NAME, APP_URL, AUTHOR_NAME, AUTHOR_URL } = pollData.config;
  return (
    <div
      className={styles.main}
      dangerouslySetInnerHTML={{
        __html: template(
          gettext('Generate by {{appName}} and developed by {{authorName}}'),
          {
            appName: `<a href="${APP_URL}" target="_blank">${APP_NAME}</a>`,
            authorName: `<a href="${AUTHOR_URL}" target="_blank">${AUTHOR_NAME}</a>`,
          }
        ),
      }}
    />
  );
});
