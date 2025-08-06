import { observer } from 'mobx-react-lite';
import { type FC, memo, type ReactNode } from 'react';
import { Link } from '@/Components/Button/components/index.tsx';
import { CardGroup } from '@/Components/Card/components/group';
import { CardItem } from '@/Components/Card/components/item.tsx';
import { CardMultiColContainer } from '@/Components/Card/components/multi-col-container.tsx';
import { CardSingleColContainer } from '@/Components/Card/components/single-col-container.tsx';
import { serverFetchRoute } from '@/Components/Fetch/server-fetch.ts';
import { gettext } from '@/Components/Language/index.ts';
import { Alert } from '@/Components/Utils/components/alert';
import { SearchLink } from '@/Components/Utils/components/search-link';
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
      <CardItem id={PhpInfoConstants.id} title={gettext('PHP Information')}>
        <CardMultiColContainer>
          {oneLineItems.map(([title, content]) => (
            <CardGroup key={title} label={title}>
              {content}
            </CardGroup>
          ))}
          {shortItems.map(([title, content]) => (
            <CardGroup key={title} label={title}>
              {content}
            </CardGroup>
          ))}
        </CardMultiColContainer>
        <CardSingleColContainer>
          {longItems.map(([title, content]) => (
            <CardGroup key={title} label={title}>
              {content}
            </CardGroup>
          ))}
        </CardSingleColContainer>
      </CardItem>
    );
  })
);
