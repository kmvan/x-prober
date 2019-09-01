import React, { Component } from 'react'
import { observer } from 'mobx-react'
import { gettext } from '~components/Language/src'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import store from '../stores'

@observer
class MyInfo extends Component {
  public render() {
    const { conf } = store
    const items: any[] = [
      [gettext('My IP'), conf.ip],
      [gettext('My browser UA'), navigator.userAgent],
      [gettext('My browser languages (via JS)'), navigator.languages.join(',')],
      [gettext('My browser languages (via PHP)'), conf.phpLanguage],
      [gettext('My location'), gettext('In development')],
    ]

    return (
      <Row>
        {items.map(([name, content]: [string, string]) => {
          return (
            <CardGrid key={name} title={name} desktopLg={[1, 2]}>
              {content}
            </CardGrid>
          )
        })}
      </Row>
    )
  }
}

export default MyInfo
