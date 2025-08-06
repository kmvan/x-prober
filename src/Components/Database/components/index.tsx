import { observer } from 'mobx-react-lite';
import { type FC, memo } from 'react';
import { CardGroup } from '@/Components/Card/components/group';
import { CardItem } from '@/Components/Card/components/item.tsx';
import { CardMultiColContainer } from '@/Components/Card/components/multi-col-container.tsx';
import { gettext } from '@/Components/Language/index.ts';
import { Alert } from '@/Components/Utils/components/alert';
import { DatabaseConstants } from './constants.ts';
import { DatabaseStore } from './store';
export const Database: FC = memo(
  observer(() => {
    const { pollData } = DatabaseStore;
    const shortItems: [string, boolean | string][] = [
      ['SQLite3', pollData?.sqlite3 ?? false],
      ['MySQLi client', pollData?.mysqliClientVersion ?? false],
      ['Mongo', pollData?.mongo ?? false],
      ['MongoDB', pollData?.mongoDb ?? false],
      ['PostgreSQL', pollData?.postgreSql ?? false],
      ['Paradox', pollData?.paradox ?? false],
      ['MS SQL', pollData?.msSql ?? false],
      ['PDO', pollData?.pdo ?? false],
    ];
    return (
      <CardItem id={DatabaseConstants.id} title={gettext('Database')}>
        <CardMultiColContainer>
          {shortItems.map(([name, content]) => (
            <CardGroup key={name} label={name}>
              <Alert isSuccess={Boolean(content)} msg={content} />
            </CardGroup>
          ))}
        </CardMultiColContainer>
      </CardItem>
    );
  })
);
