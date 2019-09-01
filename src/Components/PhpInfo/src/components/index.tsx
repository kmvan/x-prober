import React, { Component, ReactNode } from 'react'
import { observer } from 'mobx-react'
import { gettext } from '~components/Language/src'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import store from '../stores'
import Alert from '~components/Helper/src/components/alert'
import MultiItemContainer from '~components/Card/src/components/multi-item-container'
import SearchLink from '~components/Helper/src/components/search-link'
import PhpInfoPhpVersion from './php-version'

@observer
class PhpInfo extends Component {
  public render() {
    const { conf } = store
    const oneLineItems: Array<[string, ReactNode]> = [
      [
        'PHP info',
        <a key='phpInfoDetail' href='?action=phpInfo' target='_blank'>
          {gettext('👆 Click for detail')}
        </a>,
      ],
      [gettext('Version'), <PhpInfoPhpVersion key='phpVersion' />],
    ]
    const shortItems = [
      [gettext('SAPI interface'), conf.sapi],
      [
        gettext('Display errors'),
        <Alert key='displayErrors' isSuccess={conf.displayErrors} />,
      ],
      [gettext('Error reporting'), conf.errorReporting],
      [gettext('Max memory limit'), conf.memoryLimit],
      [gettext('Max POST size'), conf.postMaxSize],
      [gettext('Max upload size'), conf.uploadMaxFilesize],
      [gettext('Max input variables'), conf.maxInputVars],
      [gettext('Max execution time'), conf.maxExecutionTime],
      [gettext('Timeout for socket'), conf.defaultSocketTimeout],
      [
        gettext('Treatment URLs file'),
        <Alert key='allowUrlFopen' isSuccess={conf.allowUrlFopen} />,
      ],
      [gettext('SMTP support'), <Alert key='smtp' isSuccess={conf.smtp} />],
    ]

    const { disableFunctions, disableClasses } = conf

    disableFunctions.sort()
    disableClasses.sort()

    const longItems: Array<[string, ReactNode]> = [
      [
        gettext('Disabled functions'),
        disableFunctions.length
          ? disableFunctions.map((fn: string, i: number) => (
              <SearchLink key={i} keyword={fn} />
            ))
          : '-',
      ],
      [
        gettext('Disabled classes'),
        disableClasses.length
          ? disableClasses.map((fn: string, i: number) => (
              <SearchLink key={i} keyword={fn} />
            ))
          : '-',
      ],
    ]

    return (
      <Row>
        {oneLineItems.map(([title, content]) => {
          return (
            <CardGrid
              key={title}
              title={title}
              tablet={[1, 3]}
              desktopMd={[1, 4]}
              desktopLg={[1, 5]}
            >
              {content}
            </CardGrid>
          )
        })}
        {shortItems.map(([title, content]) => {
          return (
            <CardGrid
              key={title}
              title={title}
              mobileMd={[1, 2]}
              tablet={[1, 3]}
              desktopMd={[1, 4]}
              desktopLg={[1, 5]}
            >
              {content}
            </CardGrid>
          )
        })}
        {longItems.map(([title, content]) => {
          return (
            <CardGrid key={title} title={title}>
              <MultiItemContainer>{content}</MultiItemContainer>
            </CardGrid>
          )
        })}
      </Row>
    )
  }
}

export default PhpInfo
