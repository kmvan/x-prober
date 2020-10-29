import React from 'react'
import { gettext } from '@/Language/src'
import Row from '@/Grid/src/components/row'
import CardGrid from '@/Card/src/components/card-grid'
import store from '../stores'
import { observer } from 'mobx-react-lite'

const MyInfo = observer(() => {
  const { conf } = store
  const items: any[] = [
    [gettext('My IP'), conf?.ip],
    [gettext('My browser UA'), navigator.userAgent],
    [gettext('My browser languages (via JS)'), navigator.languages.join(',')],
    [gettext('My browser languages (via PHP)'), conf?.phpLanguage],
    [gettext('My location'), gettext('In development')],
  ]

  return (
    <Row>
      {items.map(([name, content]: [string, string]) => {
        return (
          <CardGrid key={name} name={name} desktopLg={[1, 2]}>
            {content}
          </CardGrid>
        )
      })}
    </Row>
  )
})

export default MyInfo
