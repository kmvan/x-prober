import React, { Component } from 'react'
import { observer } from 'mobx-react'
import { gettext } from '~components/Language/src'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import store from '../stores'
import Alert from '~components/Helper/src/components/alert'
import { orderBy } from 'lodash-es'
import SearchLink from '~components/Helper/src/components/search-link'
import MultiItemContainer from '~components/Card/src/components/multi-item-container'

@observer
class PhpExtensions extends Component {
  public render() {
    const { conf } = store
    let shortItems: any[] = [
      [gettext('Redis'), conf.redis],
      [gettext('SQLite3'), conf.sqlite3],
      [gettext('Memcache'), conf.memcache],
      [gettext('Memcached'), conf.memcached],
      [gettext('Opcache'), conf.opcache],
      [gettext('Opcache enabled'), conf.opcacheEnabled],
      [gettext('Swoole'), conf.swoole],
      [gettext('Image Magick'), conf.imagick],
      [gettext('Graphics Magick'), conf.gmagick],
      [gettext('Exif'), conf.exif],
      [gettext('Fileinfo'), conf.fileinfo],
      [gettext('SimpleXML'), conf.simplexml],
      [gettext('Sockets'), conf.sockets],
      [gettext('MySQLi'), conf.mysqli],
      [gettext('Zip'), conf.zip],
      [gettext('Multibyte String'), conf.mb_substr],
      [gettext('Phalcon'), conf.phalcon],
      [gettext('Xdebug'), conf.xdebug],
      [gettext('Zend Otimizer'), conf.zendOtimizer],
      [gettext('ionCube'), conf.ionCube],
      [gettext('SourceGuardian'), conf.sourceGuardian],
      [gettext('LDAP'), conf.ldap],
      [gettext('cURL'), conf.curl],
    ]

    shortItems = orderBy(shortItems, (o: [string, boolean]) =>
      o[0].toLowerCase()
    )

    const longItems: string[] = conf.loadedExtensions.sort()

    return (
      <Row>
        {shortItems.map(([name, enabled]: [string, boolean]) => {
          return (
            <CardGrid key={name} title={name} tablet={[1, 3]}>
              <Alert isSuccess={enabled} />
            </CardGrid>
          )
        })}
        <CardGrid title={gettext('Loaded extensions')} tablet={[1, 1]}>
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
