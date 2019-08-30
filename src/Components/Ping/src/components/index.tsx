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
import { DARK_COLOR } from '~components/Config/src'

const PingBtn = styled.a`
  display: block;
  text-align: center;
`

const PingItemContainer = styled.ul`
  display: flex;
  flex-wrap: wrap;
  background: ${DARK_COLOR};
  color: #ccc;
  padding: 0.5rem 1rem;
  margin: 0.5rem 0 0;
  max-height: 8rem;
  overflow-y: auto;
  border-radius: 1rem 1rem 0 0;
  box-shadow: inset 0 10px 10px rgba(0, 0, 0, 0.1);
  list-style-type: none;
`

const PingItem = styled.li`
  flex: 0 0 20%;
`
const PingItemNumber = styled.span`
  opacity: 0.5;
`
const PingItemLine = styled.span`
  opacity: 0.3;
`
const PingItemTime = styled.span`
  font-weight: bold;
`

interface IPingResult {
  hasPing: boolean
}

const PingResult = styled.div<IPingResult>`
  display: flex;
  align-items: center;
  background: ${DARK_COLOR};
  color: #ccc;
  border-radius: ${({ hasPing }) => (hasPing ? 0 : '1rem')}
    ${({ hasPing }) => (hasPing ? 0 : '1rem')} 1rem 1rem;
  padding: 0.5rem 1rem;
  border-top: 1px dashed #ffffff1a;
  flex-wrap: wrap;
  justify-content: space-between;
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
          <PingItemNumber>{i + 1 < 10 ? `0${i + 1}` : i + 1}</PingItemNumber>
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

    const avg = pingItemsCount
      ? Math.floor(sumBy(pingItems, 'time') / pingItemsCount)
      : 0
    const max = pingItemsCount
      ? Number((maxBy(pingItems, 'time') as IPingItem).time)
      : 0
    const min = pingItemsCount
      ? Number((minBy(pingItems, 'time') as IPingItem).time)
      : 0

    return (
      <PingResult hasPing={!!pingItemsCount}>
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
