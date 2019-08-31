import React, { Component } from 'react'
import { observer } from 'mobx-react'
import store from '../stores'
import ConfigStore from '~components/Config/src/stores'
import { gettext } from '~components/Language/src'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import restfulFetch from '~components/Fetch/src/restful-fetch'
import { OK, TOO_MANY_REQUESTS } from '~components/Restful/src/http-status'
import { template } from 'lodash-es'
import CardError from '~components/Card/src/components/error'
import CardRuby from '~components/Card/src/components/card-ruby'

@observer
class ServerBenchmark extends Component {
  public onClick = async () => {
    const { isLoading, setIsLoading, setMarks, setLinkText } = store

    if (isLoading) {
      return false
    }

    setLinkText(gettext('⏳ Testing, please wait...'))
    setIsLoading(true)

    await restfulFetch('benchmark')
      .then(([{ status }, { marks, seconds }]) => {
        if (status === OK) {
          setMarks(marks)
          setLinkText('')
        } else if (status === TOO_MANY_REQUESTS) {
          const secondsMsg = template(
            gettext('⏳ Please wait <%= seconds %>s')
          )({
            seconds,
          })
          setLinkText(secondsMsg)
        }
      })
      .catch(err => {
        setLinkText(gettext('Network error, please try again later.'))
      })

    setIsLoading(false)
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

    return items.map(({ name, url, date, proberUrl, binUrl, detail }) => {
      if (!detail) {
        return
      }

      const { hash, intLoop, floatLoop, ioLoop } = detail || {
        hash: 0,
        intLoop: 0,
        floatLoop: 0,
        ioLoop: 0,
      }

      const proberLink = proberUrl ? (
        <a
          href={proberUrl}
          target='_blank'
          title={gettext('Visit prober page')}
        >
          {' 🔗 '}
        </a>
      ) : (
        ''
      )

      const binLink = binUrl ? (
        <a href={binUrl} target='_blank' title={gettext('Download speed test')}>
          {' ⬇️ '}
        </a>
      ) : (
        ''
      )

      const title = (
        <a
          href={url}
          target='_blank'
          title={gettext('Visit the official website')}
        >
          {name}
        </a>
      )

      return (
        <CardGrid key={name} title={title} tablet={[1, 2]} desktopSm={[1, 3]}>
          {this.renderResult({
            hash,
            intLoop,
            floatLoop,
            ioLoop,
            date,
          })}
          {proberLink}
          {binLink}
        </CardGrid>
      )
    })
  }

  private renderResult({
    hash,
    intLoop,
    floatLoop,
    ioLoop,
    date,
  }: {
    hash: number
    intLoop: number
    floatLoop: number
    ioLoop: number
    date?: string
  }) {
    return (
      <>
        <CardRuby ruby={hash.toLocaleString()} rt='HASH' />
        {' + '}
        <CardRuby ruby={intLoop.toLocaleString()} rt='INT' />
        {' + '}
        <CardRuby ruby={floatLoop.toLocaleString()} rt='FLOAT' />
        {' + '}
        <CardRuby ruby={ioLoop.toLocaleString()} rt='IO' />
        {' = '}
        <CardRuby
          isResult={true}
          ruby={(hash + intLoop + floatLoop + ioLoop).toLocaleString()}
          rt={date || ''}
        />
      </>
    )
  }

  private renderTestBtn() {
    const { marks, linkText } = store

    const marksText = marks ? this.renderResult(marks) : ''
    return (
      <CardGrid title={gettext('My server')} tablet={[1, 2]} desktopSm={[1, 3]}>
        <a onClick={this.onClick}>
          {linkText} {marksText}
        </a>
      </CardGrid>
    )
  }

  public render() {
    return (
      <Row>
        {this.renderTestBtn()}
        {this.renderItems()}
      </Row>
    )
  }
}

export default ServerBenchmark
