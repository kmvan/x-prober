import { observer } from 'mobx-react-lite';
import { type FC, memo, type ReactNode } from 'react';
import { Link } from '@/Components/Button/components/index.tsx';
import { serverFetchRoute } from '@/Components/Fetch/server-fetch.ts';
import { gettext } from '@/Components/Language/index.ts';
import { ModuleGroup } from '@/Components/Module/components/group.tsx';
import { ModuleItem } from '@/Components/Module/components/item.tsx';
import { Alert } from '@/Components/Utils/components/alert';
import { SearchLink } from '@/Components/Utils/components/search-link';
import { UiMultiColContainer } from '@/Components/ui/col/multi-container.tsx';
import { UiSingleColContainer } from '@/Components/ui/col/single-container.tsx';
import { PhpInfoConstants } from './constants.ts';
import { PhpInfoPhpVersion } from './php-version';
import { PhpInfoStore } from './store.ts';
export const PhpInfo: FC = memo(
  observer(() => {
    const { pollData } = PhpInfoStore;
    if (!pollData) {
      return null;
    }
    const oneLineItems: [string, ReactNode][] = [
      [
        'PHP info',
        <Link
          href={serverFetchRoute('phpInfoDetail')}
          key="phpInfoDetail"
          target="_blank"
        >
          {gettext('Detail')}
        </Link>,
      ],
      [gettext('Version'), <PhpInfoPhpVersion key="phpVersion" />],
    ];
    const shortItems: [string, ReactNode][] = [
      [gettext('SAPI interface'), pollData?.sapi],
      [
        gettext('Display errors'),
        <Alert isSuccess={pollData?.displayErrors} key="displayErrors" />,
      ],
      [gettext('Error reporting'), pollData.errorReporting],
      [gettext('Max memory limit'), pollData.memoryLimit],
      [gettext('Max POST size'), pollData.postMaxSize],
      [gettext('Max upload size'), pollData.uploadMaxFilesize],
      [gettext('Max input variables'), pollData.maxInputVars],
      [gettext('Max execution time'), pollData.maxExecutionTime],
      [gettext('Timeout for socket'), pollData.defaultSocketTimeout],
      [
        gettext('Treatment URLs file'),
        <Alert isSuccess={pollData.allowUrlFopen} key="allowUrlFopen" />,
      ],
      [gettext('SMTP support'), <Alert isSuccess={pollData.smtp} key="smtp" />],
    ];
    const { disableFunctions, disableClasses } = pollData;
    disableFunctions.slice().sort();
    disableClasses.slice().sort();
    const longItems: [string, ReactNode][] = [
      [
        gettext('Disabled functions'),
        disableFunctions.length
          ? disableFunctions.map((fn: string) => (
              <SearchLink key={fn} keyword={fn} />
            ))
          : '-',
      ],
      [
        gettext('Disabled classes'),
        disableClasses.length
          ? disableClasses.map((fn: string) => (
              <SearchLink key={fn} keyword={fn} />
            ))
          : '-',
      ],
    ];
    return (
      <ModuleItem id={PhpInfoConstants.id} title={gettext('PHP Information')}>
        <UiMultiColContainer>
          {oneLineItems.map(([title, content]) => (
            <ModuleGroup key={title} label={title}>
              {content}
            </ModuleGroup>
          ))}
          {shortItems.map(([title, content]) => (
            <ModuleGroup key={title} label={title}>
              {content}
            </ModuleGroup>
          ))}
        </UiMultiColContainer>
        <UiSingleColContainer>
          {longItems.map(([title, content]) => (
            <ModuleGroup key={title} label={title}>
              {content}
            </ModuleGroup>
          ))}
        </UiSingleColContainer>
      </ModuleItem>
    );
  })
);
