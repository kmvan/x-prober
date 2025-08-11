import { observer } from 'mobx-react-lite';
import { type FC, memo } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { ModuleGroup } from '@/Components/Module/components/group.tsx';
import { ModuleItem } from '@/Components/Module/components/item.tsx';
import { UiMultiColContainer } from '@/Components/ui/col/multi-container.tsx';
import { EnableStatus } from '@/Components/ui/enable-status/index.tsx';
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
      <ModuleItem id={DatabaseConstants.id} title={gettext('Database')}>
        <UiMultiColContainer>
          {shortItems.map(([name, content]) => (
            <ModuleGroup key={name} label={name}>
              <EnableStatus isEnable={Boolean(content)} text={content} />
            </ModuleGroup>
          ))}
        </UiMultiColContainer>
      </ModuleItem>
    );
  })
);
