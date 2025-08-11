import { observer } from 'mobx-react-lite';
import { type FC, memo } from 'react';
import { gettext } from '@/Components/Language/index.ts';
import { ModuleGroup } from '@/Components/Module/components/group.tsx';
import { ModuleItem } from '@/Components/Module/components/item.tsx';
import { SearchLink } from '@/Components/Utils/components/search-link';
import { UiMultiColContainer } from '@/Components/ui/col/multi-container.tsx';
import { UiSingleColContainer } from '@/Components/ui/col/single-container.tsx';
import { EnableStatus } from '@/Components/ui/enable-status/index.tsx';
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
      <ModuleItem
        id={PhpExtensionsConstants.id}
        title={gettext('PHP Extensions')}
      >
        <UiMultiColContainer>
          {shortItems.map(([name, enabled]) => (
            <ModuleGroup key={name} label={name}>
              <EnableStatus isEnable={enabled} />
            </ModuleGroup>
          ))}
        </UiMultiColContainer>
        <UiSingleColContainer>
          {Boolean(longItems.length) && (
            <ModuleGroup label={gettext('Loaded extensions')}>
              {longItems.map((id) => (
                <SearchLink key={id} keyword={id} />
              ))}
            </ModuleGroup>
          )}
        </UiSingleColContainer>
      </ModuleItem>
    );
  })
);
