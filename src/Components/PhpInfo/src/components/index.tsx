import React, { Component } from 'react'
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

    const shortItems = [
      [
        gettext('PHP info detail'),
        <a key='phpInfoDetail' href='?action=phpInfo' target='_blank'>
          {'👆 Click for detail'}
        </a>,
      ],
      [gettext('Version'), <PhpInfoPhpVersion key='phpVersion' />],
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
    const longItems = [
      [
        gettext('Disabled functions'),
        conf.disableFunctions.length
          ? conf.disableFunctions.map((fn: string, i: number) => (
              <SearchLink key={i} keyword={fn} />
            ))
          : '-',
      ],
      [
        gettext('Disabled classes'),
        conf.disableClasses.length
          ? conf.disableClasses.map((fn: string, i: number) => (
              <SearchLink key={i} keyword={fn} />
            ))
          : '-',
      ],
    ]

    return (
      <Row>
        {shortItems.map(([title, content]) => {
          return (
            <CardGrid key={title} title={title} tablet={[1, 3]}>
              {content}
            </CardGrid>
          )
        })}
        {longItems.map(([title, content]) => {
          return (
            <CardGrid key={title} title={title} tablet={[1, 1]}>
              <MultiItemContainer>{content}</MultiItemContainer>
            </CardGrid>
          )
        })}
      </Row>
    )
  }
}

export default PhpInfo
