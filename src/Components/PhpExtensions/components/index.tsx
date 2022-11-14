import { FC } from 'react'
import { CardGrid } from '../../Card/components/card-grid'
import { MultiItemContainer } from '../../Card/components/multi-item-container'
import { GridContainer } from '../../Grid/components/container'
import { gettext } from '../../Language'
import { Alert } from '../../Utils/components/alert'
import { SearchLink } from '../../Utils/components/search-link'
import { PhpExtensionsConstants } from '../constants'
const { conf } = PhpExtensionsConstants
const shortItems: [string, boolean][] = [
  ['Redis', Boolean(conf?.redis)],
  ['SQLite3', Boolean(conf?.sqlite3)],
  ['Memcache', Boolean(conf?.memcache)],
  ['Memcached', Boolean(conf?.memcached)],
  ['Opcache', Boolean(conf?.opcache)],
  [gettext('Opcache enabled'), Boolean(conf?.opcacheEnabled)],
  [gettext('Opcache JIT enabled'), Boolean(conf?.opcacheJitEnabled)],
  ['Swoole', Boolean(conf?.swoole)],
  ['Image Magick', Boolean(conf?.imagick)],
  ['Graphics Magick', Boolean(conf?.gmagick)],
  ['Exif', Boolean(conf?.exif)],
  ['Fileinfo', Boolean(conf?.fileinfo)],
  ['SimpleXML', Boolean(conf?.simplexml)],
  ['Sockets', Boolean(conf?.sockets)],
  ['MySQLi', Boolean(conf?.mysqli)],
  ['Zip', Boolean(conf?.zip)],
  ['Multibyte String', Boolean(conf?.mbstring)],
  ['Phalcon', Boolean(conf?.phalcon)],
  ['Xdebug', Boolean(conf?.xdebug)],
  ['Zend Optimizer', Boolean(conf?.zendOptimizer)],
  ['ionCube', Boolean(conf?.ionCube)],
  ['Source Guardian', Boolean(conf?.sourceGuardian)],
  ['LDAP', Boolean(conf?.ldap)],
  ['cURL', Boolean(conf?.curl)],
]
shortItems.sort((a, b) => {
  const x = a[0].toLowerCase()
  const y = b[0].toLowerCase()
  if (x < y) {
    return -1
  }
  if (x > y) {
    return 1
  }
  return 0
})
const longItems: string[] = conf?.loadedExtensions || []
longItems.sort((a, b) => {
  const x = a.toLowerCase()
  const y = b.toLowerCase()
  if (x < y) {
    return -1
  }
  if (x > y) {
    return 1
  }
  return 0
})
export const PhpExtensions: FC = () => (
  <GridContainer>
    {shortItems.map(([name, enabled]) => (
      <CardGrid key={name} name={name} lg={2} xl={3} xxl={4}>
        <Alert isSuccess={enabled} />
      </CardGrid>
    ))}
    {Boolean(longItems.length) && (
      <CardGrid name={gettext('Loaded extensions')}>
        <MultiItemContainer>
          {longItems.map((id) => (
            <SearchLink key={id} keyword={id} />
          ))}
        </MultiItemContainer>
      </CardGrid>
    )}
  </GridContainer>
)
