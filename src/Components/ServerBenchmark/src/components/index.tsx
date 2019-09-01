import React, { Component } from 'react'
import { observer } from 'mobx-react'
import store from '../stores'
import { gettext } from '~components/Language/src'
import Row from '~components/Grid/src/components/row'
import CardGrid from '~components/Card/src/components/card-grid'
import restfulFetch from '~components/Fetch/src/restful-fetch'
import { OK, TOO_MANY_REQUESTS } from '~components/Restful/src/http-status'
import { template, sum, orderBy } from 'lodash-es'
import CardError from '~components/Card/src/components/error'
import CardRuby from '~components/Card/src/components/card-ruby'
import { toJS } from 'mobx'

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
          if (marks) {
            setMarks(marks)
            setLinkText('')
          } else {
            setLinkText(gettext('Network error, please try again later.'))
          }
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
    const { servers } = store

    if (!servers) {
      return (
        <CardError>
          {gettext('Can not fetch marks data from GitHub.')}
        </CardError>
      )
    }

    let items = toJS(servers).map(item => {
      item.total = item.detail ? sum(Object.values(item.detail)) : 0

      return item
    })

    items = orderBy(items, ({ total }) => total).reverse()

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
        <CardGrid
          key={name}
          title={title}
          tablet={[1, 2]}
          desktopMd={[1, 3]}
          desktopLg={[1, 4]}
        >
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
      <CardGrid
        title={gettext('My server')}
        tablet={[1, 2]}
        desktopMd={[1, 3]}
        desktopLg={[1, 4]}
      >
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
