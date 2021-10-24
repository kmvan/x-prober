import React, { FC } from 'react'
import { CardGrid } from '../../../Card/src/components/card-grid'
import { MultiItemContainer } from '../../../Card/src/components/multi-item-container'
import { Row } from '../../../Grid/src/components/row'
import { gettext } from '../../../Language/src'
import { Alert } from '../../../Utils/src/components/alert'
import { SearchLink } from '../../../Utils/src/components/search-link'
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
  <Row>
    {shortItems.map(([name, enabled]) => (
      <CardGrid
        key={name}
        name={name}
        mobileMd={[1, 2]}
        tablet={[1, 3]}
        desktopMd={[1, 4]}
        desktopLg={[1, 5]}
      >
        <Alert isSuccess={enabled} />
      </CardGrid>
    ))}
    {Boolean(longItems.length) && (
      <CardGrid name={gettext('Loaded extensions')} tablet={[1, 1]}>
        <MultiItemContainer>
          {longItems.map((id) => (
            <SearchLink key={id} keyword={id} />
          ))}
        </MultiItemContainer>
      </CardGrid>
    )}
  </Row>
)
