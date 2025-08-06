import { observer } from 'mobx-react-lite';
import { type FC, memo } from 'react';
import { CardGroup } from '@/Components/Card/components/group.tsx';
import { CardItem } from '@/Components/Card/components/item.tsx';
import { CardMultiColContainer } from '@/Components/Card/components/multi-col-container.tsx';
import { CardSingleColContainer } from '@/Components/Card/components/single-col-container.tsx';
import { gettext } from '@/Components/Language/index.ts';
import { Alert } from '@/Components/Utils/components/alert';
import { SearchLink } from '@/Components/Utils/components/search-link';
import { PhpExtensionsConstants } from './constants.ts';
import { PhpExtensionsStore } from './store.ts';
export const PhpExtensions: FC = memo(
  observer(() => {
    const { pollData } = PhpExtensionsStore;
    if (!pollData) {
      return null;
    }
    const shortItems: [string, boolean][] = [
      ['Redis', Boolean(pollData.redis)],
      ['SQLite3', Boolean(pollData.sqlite3)],
      ['Memcache', Boolean(pollData.memcache)],
      ['Memcached', Boolean(pollData.memcached)],
      ['Opcache', Boolean(pollData.opcache)],
      [gettext('Opcache enabled'), Boolean(pollData.opcacheEnabled)],
      [gettext('Opcache JIT enabled'), Boolean(pollData.opcacheJitEnabled)],
      ['Swoole', Boolean(pollData.swoole)],
      ['Image Magick', Boolean(pollData.imagick)],
      ['Graphics Magick', Boolean(pollData.gmagick)],
      ['Exif', Boolean(pollData.exif)],
      ['Fileinfo', Boolean(pollData.fileinfo)],
      ['SimpleXML', Boolean(pollData.simplexml)],
      ['Sockets', Boolean(pollData.sockets)],
      ['MySQLi', Boolean(pollData.mysqli)],
      ['Zip', Boolean(pollData.zip)],
      ['Multibyte String', Boolean(pollData.mbstring)],
      ['Phalcon', Boolean(pollData.phalcon)],
      ['Xdebug', Boolean(pollData.xdebug)],
      ['Zend Optimizer', Boolean(pollData.zendOptimizer)],
      ['ionCube', Boolean(pollData.ionCube)],
      ['Source Guardian', Boolean(pollData.sourceGuardian)],
      ['LDAP', Boolean(pollData.ldap)],
      ['cURL', Boolean(pollData.curl)],
    ];
    shortItems.slice().sort((a, b) => {
      const x = a[0].toLowerCase();
      const y = b[0].toLowerCase();
      if (x < y) {
        return -1;
      }
      if (x > y) {
        return 1;
      }
      return 0;
    });
    const longItems: string[] = pollData.loadedExtensions || [];
    longItems.slice().sort((a, b) => {
      const x = a.toLowerCase();
      const y = b.toLowerCase();
      if (x < y) {
        return -1;
      }
      if (x > y) {
        return 1;
      }
      return 0;
    });
    return (
      <CardItem
        id={PhpExtensionsConstants.id}
        title={gettext('PHP Extensions')}
      >
        <CardMultiColContainer>
          {shortItems.map(([name, enabled]) => (
            <CardGroup key={name} label={name}>
              <Alert isSuccess={enabled} />
            </CardGroup>
          ))}
        </CardMultiColContainer>
        <CardSingleColContainer>
          {Boolean(longItems.length) && (
            <CardGroup label={gettext('Loaded extensions')}>
              {longItems.map((id) => (
                <SearchLink key={id} keyword={id} />
              ))}
            </CardGroup>
          )}
        </CardSingleColContainer>
      </CardItem>
    );
  })
);
