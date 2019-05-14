import React, { Component } from 'react'
import { observer } from 'mobx-react'
import store from '../stores'
import Portal from '~components/Helper/src/components/portal'
import fetchServer from '~components/Helper/src/components/fetch-server'
import './style/index.scss'
import classNames from 'classnames'
import { sumBy, maxBy, minBy } from 'lodash-es'

@observer
class MyInfo extends Component {
  private pingContainer = document.querySelector(
    '.inn-ping__container'
  ) as HTMLElement
  private store = store

  private pingTimer

  private onClickPing = async () => {
    if (this.store.isPing) {
      this.store.setIsPing(false)
      clearTimeout(this.pingTimer)

      return
    }

    this.store.setIsPing(true)
    await this.pingLoop()
  }

  private pingLoop = async () => {
    await this.ping()
    this.pingTimer = setTimeout(async () => {
      await this.pingLoop()
    }, 1000)
  }

  private ping = async () => {
    const {
      refs: { itemContainer },
      appendPingItem,
    } = this.store
    const start = +new Date()
    const res = await fetchServer({
      action: 'ping',
    })
    const end = +new Date()

    if (res && res.code === 0) {
      const serverTime = res.data.time * 1000
      appendPingItem({
        time: Math.floor(end - start - serverTime),
      })

      setTimeout(() => {
        if (itemContainer.scrollTop < itemContainer.scrollHeight) {
          itemContainer.scrollTop = itemContainer.scrollHeight
        }
      }, 10)
    }
  }

  private renderItems() {
    const { pingItemsCount, pingItems, setRef } = this.store

    if (!pingItemsCount) {
      return
    }

    const items = pingItems.map(({ time }, i) => {
      return (
        <li key={i} className="inn-ping__item">
          <span className="inn-ping__item__no">{i + 1}</span>
          <span className="inn-ping__item__line">{' ------------ '}</span>
          <span className="inn-ping__item__time">{`${time} ms`}</span>
        </li>
      )
    })

    return (
      <ul
        className="inn-ping__item__container"
        ref={c => setRef('itemContainer', c)}
      >
        {items}
      </ul>
    )
  }

  private renderResults() {
    const {
      pingItemsCount,
      pingItems,
      conf: { lang },
    } = this.store

    if (!pingItemsCount) {
      return
    }

    const avg = Math.floor(sumBy(pingItems, 'time') / pingItemsCount)
    const max = Number(maxBy(pingItems, 'time').time)
    const min = Number(minBy(pingItems, 'time').time)

    return (
      <div className="inn-ping__results">
        <span className="inn-ping__results__times">
          {lang.times.replace('%d', pingItemsCount)}{' '}
        </span>
        <span className="inn-ping__results__avg">
          {lang.minAvgMax.replace('%s', `${min}/${avg}/${max} ms`)}
        </span>
      </div>
    )
  }
  private renderPing() {
    if (!this.pingContainer) {
      return null
    }

    const { isPing } = this.store
    const btnClassName = classNames({
      'inn-ping__btn': true,
      'is-active': isPing,
    })
    return (
      <Portal target={this.pingContainer}>
        <>
          <a onClick={this.onClickPing} className={btnClassName}>
            Ping
          </a>
          {this.renderItems()}
          {this.renderResults()}
        </>
      </Portal>
    )
  }

  public render() {
    return <>{this.renderPing()}</>
  }
}

export default MyInfo
