import React, { Component } from 'react'
import { observer } from 'mobx-react'
import store from '../stores'
import ConfigStore from '~components/Config/src/stores'
import { gettext } from '~components/Language/src'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import restfulFetch from '~components/Fetch/src/restful-fetch'
import { OK, TOO_MANY_REQUESTS } from '~components/Restful/src/http-status'
import { template, get } from 'lodash-es'
import CardError from '~components/Card/src/components/error'

@observer
class ServerBenchmark extends Component {
  public onClick = async () => {
    const { isLoading, totalMarks } = store

    if (isLoading) {
      return false
    }

    store.setLinkText(gettext('Testing, please wait...'))
    store.setIsLoading(true)

    await restfulFetch('benchmark')
      .then(([{ status }, { points, total, seconds }]) => {
        if (status === OK) {
          store.setMarks(points)
          store.setLinkText(`✓ ${total.toLocaleString()}`)
        } else if (status === TOO_MANY_REQUESTS) {
          const msg = totalMarks
            ? template(gettext('Please wait <%= seconds %>'))({ seconds }) +
              ` (${totalMarks})`
            : template(gettext('Please wait <%= seconds %>'))({ seconds })
          store.setLinkText(msg)
        }
      })
      .catch(err => {
        store.setLinkText(gettext('Error'))
      })

    store.setIsLoading(false)
  }

  private renderItems() {
    const { appConfig } = ConfigStore

    if (!appConfig) {
      return (
        <CardError>
          {gettext('Can not fetch marks data from GitHub.')}
        </CardError>
      )
    }

    const items = appConfig.BENCHMARKS || []

    return items.map(({ label, total, url, date, proberUrl, detail }) => {
      const { hash, intLoop, floatLoop, ioLoop } = detail || {
        hash: 0,
        intLoop: 0,
        floatLoop: 0,
        ioLoop: 0,
      }

      const title = url ? (
        <a href={url} target='_blank'>
          {label}
        </a>
      ) : (
        label
      )

      return (
        <CardGrid key={label} title={title} tablet={[1, 2]} mobileSm={[1, 3]}>
          {total ||
            `${hash}+${intLoop}+${floatLoop}+${ioLoop}=${hash +
              intLoop +
              floatLoop +
              ioLoop}`}
        </CardGrid>
      )
    })
  }

  public render() {
    return (
      <Row>
        <CardGrid
          title={gettext('My server')}
          tablet={[1, 2]}
          mobileSm={[1, 3]}
        >
          <a onClick={this.onClick} title={store.linkTitle}>
            {store.linkText}
          </a>
        </CardGrid>
        {this.renderItems()}
      </Row>
    )
  }
}

export default ServerBenchmark
