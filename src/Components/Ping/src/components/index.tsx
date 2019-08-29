import React, { Component } from 'react'
import { observer } from 'mobx-react'
import store, { IPingItem } from '../stores'
import { sumBy, maxBy, minBy, template } from 'lodash-es'
import Row from '~components/Grid/src/components/row'
import { gettext } from '~components/Language/src'
import CardGrid from '~components/Card/src/components/card-grid'
import styled from 'styled-components'
import restfulFetch from '~components/Fetch/src/restful-fetch'
import { OK } from '~components/Restful/src/http-status'

const PingBtn = styled.a``

const PingItemContainer = styled.ul``

const PingItem = styled.li``
const PingItemNumber = styled.span``
const PingItemLine = styled.span``
const PingItemTime = styled.span``

const PingResult = styled.div`
  display: flex;
`
const PingResultTimes = styled.div``
const PingResultAvg = styled.div``

@observer
class Ping extends Component {
  private pingTimer: number = 0

  private onClickPing = async () => {
    const { isPing, setIsPing } = store

    if (isPing) {
      setIsPing(false)
      clearTimeout(this.pingTimer)

      return
    }

    setIsPing(true)
    await this.pingLoop()
  }

  private pingLoop = async () => {
    await this.ping()
    this.pingTimer = window.setTimeout(async () => {
      await this.pingLoop()
    }, 1000)
  }

  private ping = async () => {
    const {
      refs: { itemContainer },
      appendPingItem,
    } = store
    const start = +new Date()

    await restfulFetch('ping')
      .then(([{ status }, { time }]) => {
        if (status === OK) {
          const end = +new Date()
          const serverTime = time * 1000
          appendPingItem({
            time: Math.floor(end - start - serverTime),
          })

          setTimeout(() => {
            if (itemContainer.scrollTop < itemContainer.scrollHeight) {
              itemContainer.scrollTop = itemContainer.scrollHeight
            }
          }, 10)
        }
      })
      .catch(err => {})
  }

  private renderItems() {
    const { pingItemsCount, pingItems, setRef } = store

    if (!pingItemsCount) {
      return
    }

    const items = pingItems.map(({ time }, i) => {
      return (
        <PingItem key={i}>
          <PingItemNumber>{i + 1}</PingItemNumber>
          <PingItemLine>{' ------------ '}</PingItemLine>
          <PingItemTime>{`${time} ms`}</PingItemTime>
        </PingItem>
      )
    })

    return (
      <PingItemContainer ref={c => setRef('itemContainer', c)}>
        {items}
      </PingItemContainer>
    )
  }

  private renderResults() {
    const { pingItemsCount, pingItems } = store

    if (!pingItemsCount) {
      return
    }

    const avg = Math.floor(sumBy(pingItems, 'time') / pingItemsCount)
    const max = Number((maxBy(pingItems, 'time') as IPingItem).time)
    const min = Number((minBy(pingItems, 'time') as IPingItem).time)

    return (
      <PingResult>
        <PingResultTimes>
          {template(gettext('Times: <%= times %>'))({ times: pingItemsCount })}
        </PingResultTimes>
        <PingResultAvg>
          {template(
            gettext('Min: <%= min %> / Max: <%= max %> / Avg: <%= avg %>')
          )({ min, max, avg })}
        </PingResultAvg>
      </PingResult>
    )
  }

  private pingBtn() {
    return (
      <PingBtn onClick={this.onClickPing}>
        {store.isPing ? gettext('Stop ping') : gettext('Start ping')}
      </PingBtn>
    )
  }

  public render() {
    return (
      <Row>
        <CardGrid title={this.pingBtn()} tablet={[1, 1]}>
          {this.renderItems()}
          {this.renderResults()}
        </CardGrid>
      </Row>
    )
  }
}

export default Ping
