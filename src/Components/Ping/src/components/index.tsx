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
import { COLOR_DARK, GUTTER } from '~components/Config/src'
import { device } from '~components/Style/src/components/devices'

const StyledPingBtn = styled.a`
  display: block;
  text-align: center;
`

const StyledPingItemContainer = styled.ul`
  display: flex;
  flex-wrap: wrap;
  background: ${COLOR_DARK};
  color: #ccc;
  padding: 0.5rem ${GUTTER};
  margin: 0.5rem 0 0;
  max-height: 8rem;
  overflow-y: auto;
  border-radius: ${GUTTER} ${GUTTER} 0 0;
  box-shadow: inset 0 10px 10px rgba(0, 0, 0, 0.1);
  list-style-type: none;

  ::-webkit-scrollbar-track {
    background-color: transparent;
  }

  ::-webkit-scrollbar {
    width: ${GUTTER};
    background-color: transparent;
  }

  ::-webkit-scrollbar-thumb {
    border-radius: ${GUTTER} 0 0 ${GUTTER};
    background-color: rgba(255, 255, 255, 0.5);
    opacity: 0;

    :hover {
      opacity: 1;
    }
  }
`

const StyledPingItem = styled.li`
  flex: 0 0 ${(1 / 3) * 100}%;

  @media ${device('tablet')} {
    flex: 0 0 25%;
  }

  @media ${device('desktopSm')} {
    flex: 0 0 20%;
  }
`
const StyledPingItemNumber = styled.span`
  opacity: 0.5;
  display: none;

  @media ${device('tablet')} {
    display: inline;
  }
`
const StyledPingItemLine = styled.span`
  opacity: 0.3;
  display: none;

  @media ${device('tablet')} {
    display: inline;
  }
`
const StyledPingItemTime = styled.span`
  font-weight: bold;
`

interface IPingResult {
  hasPing: boolean
}

const StyledPingResult = styled.div<IPingResult>`
  display: flex;
  align-items: center;
  background: ${COLOR_DARK};
  color: #ccc;
  border-radius: ${({ hasPing }) => (hasPing ? 0 : GUTTER)}
    ${({ hasPing }) => (hasPing ? 0 : GUTTER)} ${GUTTER} ${GUTTER};
  padding: calc(${GUTTER} / 2) ${GUTTER};
  border-top: 1px dashed rgba(255, 255, 255, 0.1);
  flex-wrap: wrap;
  justify-content: space-between;
`
const StyledPingResultTimes = styled.div``
const StyledPingResultAvg = styled.div``

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
            if (
              itemContainer &&
              itemContainer.scrollTop < itemContainer.scrollHeight
            ) {
              itemContainer.scrollTop = itemContainer.scrollHeight
            }
          }, 100)
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
        <StyledPingItem key={i}>
          <StyledPingItemNumber>
            {i + 1 < 10 ? `0${i + 1}` : i + 1}
          </StyledPingItemNumber>
          <StyledPingItemLine>{' ------------ '}</StyledPingItemLine>
          <StyledPingItemTime>{`${time} ms`}</StyledPingItemTime>
        </StyledPingItem>
      )
    })

    return (
      <StyledPingItemContainer ref={c => setRef('itemContainer', c)}>
        {items}
      </StyledPingItemContainer>
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
      <StyledPingResult hasPing={!!pingItemsCount}>
        <StyledPingResultTimes>
          {template(gettext('Times: <%= times %>'))({ times: pingItemsCount })}
        </StyledPingResultTimes>
        <StyledPingResultAvg>
          {template(
            gettext('Min: <%= min %> / Max: <%= max %> / Avg: <%= avg %>')
          )({ min, max, avg })}
        </StyledPingResultAvg>
      </StyledPingResult>
    )
  }

  private pingBtn() {
    return (
      <StyledPingBtn onClick={this.onClickPing}>
        {store.isPing ? gettext('⏸️ Stop ping') : gettext('👆 Start ping')}
      </StyledPingBtn>
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
