import React, { Component } from 'react'
import { gettext } from '~components/Language/src'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import store from '../stores'
import Alert from '~components/Helper/src/components/alert'
import SearchLink from '~components/Helper/src/components/search-link'
import MultiItemContainer from '~components/Card/src/components/multi-item-container'

const { conf } = store
const shortItems = [
  ['Redis', conf.redis],
  ['SQLite3', conf.sqlite3],
  ['Memcache', conf.memcache],
  ['Memcached', conf.memcached],
  ['Opcache', conf.opcache],
  [gettext('Opcache enabled'), conf.opcacheEnabled],
  ['Swoole', conf.swoole],
  ['Image Magick', conf.imagick],
  ['Graphics Magick', conf.gmagick],
  ['Exif', conf.exif],
  ['Fileinfo', conf.fileinfo],
  ['SimpleXML', conf.simplexml],
  ['Sockets', conf.sockets],
  ['MySQLi', conf.mysqli],
  ['Zip', conf.zip],
  ['Multibyte String', conf.mbstring],
  ['Phalcon', conf.phalcon],
  ['Xdebug', conf.xdebug],
  ['Zend Otimizer', conf.zendOtimizer],
  ['ionCube', conf.ionCube],
  ['Source Guardian', conf.sourceGuardian],
  ['LDAP', conf.ldap],
  ['cURL', conf.curl],
].sort((a, b) => {
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

const longItems: string[] = conf.loadedExtensions.sort((a, b) => {
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

class PhpExtensions extends Component {
  public render() {
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
              desktopLg={[1, 5]}
            >
              <Alert isSuccess={enabled} />
            </CardGrid>
          )
        })}
        <CardGrid name={gettext('Loaded extensions')} tablet={[1, 1]}>
          <MultiItemContainer>
            {longItems.map(id => {
              return <SearchLink key={id} keyword={id} />
            })}
          </MultiItemContainer>
        </CardGrid>
      </Row>
    )
  }
}

export default PhpExtensions
