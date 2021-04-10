import { CardGrid } from '@/Card/src/components/card-grid'
import { MultiItemContainer } from '@/Card/src/components/multi-item-container'
import { Row } from '@/Grid/src/components/row'
import { gettext } from '@/Language/src'
import { Alert } from '@/Utils/src/components/alert'
import { SearchLink } from '@/Utils/src/components/search-link'
import React from 'react'
import { PhpExtensionsStore } from '../stores'
const { conf } = PhpExtensionsStore
const shortItems: [string, boolean][] = [
  ['Redis', !!conf?.redis],
  ['SQLite3', !!conf?.sqlite3],
  ['Memcache', !!conf?.memcache],
  ['Memcached', !!conf?.memcached],
  ['Opcache', !!conf?.opcache],
  [gettext('Opcache enabled'), !!conf?.opcacheEnabled],
  [gettext('Opcache JIT enabled'), !!conf?.opcacheJitEnabled],
  ['Swoole', !!conf?.swoole],
  ['Image Magick', !!conf?.imagick],
  ['Graphics Magick', !!conf?.gmagick],
  ['Exif', !!conf?.exif],
  ['Fileinfo', !!conf?.fileinfo],
  ['SimpleXML', !!conf?.simplexml],
  ['Sockets', !!conf?.sockets],
  ['MySQLi', !!conf?.mysqli],
  ['Zip', !!conf?.zip],
  ['Multibyte String', !!conf?.mbstring],
  ['Phalcon', !!conf?.phalcon],
  ['Xdebug', !!conf?.xdebug],
  ['Zend Optimizer', !!conf?.zendOptimizer],
  ['ionCube', !!conf?.ionCube],
  ['Source Guardian', !!conf?.sourceGuardian],
  ['LDAP', !!conf?.ldap],
  ['cURL', !!conf?.curl],
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
export function PhpExtensions() {
  return (
    <Row>
      {shortItems.map(([name, enabled]) => {
        return (
          <CardGrid
            key={name}
            name={name}
            mobileMd={[1, 2]}
            tablet={[1, 3]}
            desktopMd={[1, 4]}
            desktopLg={[1, 5]}>
            <Alert isSuccess={enabled} />
          </CardGrid>
        )
      })}
      {!!longItems.length && (
        <CardGrid name={gettext('Loaded extensions')} tablet={[1, 1]}>
          <MultiItemContainer>
            {longItems.map((id) => {
              return <SearchLink key={id} keyword={id} />
            })}
          </MultiItemContainer>
        </CardGrid>
      )}
    </Row>
  )
}
