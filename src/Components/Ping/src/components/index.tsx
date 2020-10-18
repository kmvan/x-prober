import React, { Component, RefObject, createRef } from 'react'
import { observer } from 'mobx-react'
import store from '../stores'
import template from '@/Helper/src/components/template'
import Row from '@/Grid/src/components/row'
import { gettext } from '@/Language/src'
import CardGrid from '@/Card/src/components/card-grid'
import styled from 'styled-components'
import restfulFetch from '@/Fetch/src/restful-fetch'
import { OK } from '@/Restful/src/http-status'
import { GUTTER, BORDER_RADIUS } from '@/Config/src'
import { device } from '@/Style/src/components/devices'
import { rgba } from 'polished'

const StyledPingBtn = styled.a`
  display: block;
  text-align: center;
  color: ${({ theme }) => theme.colorGray};
  background: ${({ theme }) => theme.colorDark};
  border-radius: ${BORDER_RADIUS};
  padding: calc(${GUTTER} / 2) ${GUTTER};
  text-shadow: ${({ theme }) => theme.textShadowWithDarkBg};

  :hover,
  :active {
    text-decoration: none;
    color: ${({ theme }) => theme.colorGray};
    opacity: 0.9;
  }
  :active {
    opacity: 1;
    transform: scale3d(0.95, 0.95, 1);
  }
`

const StyledPingItemContainer = styled.ul`
  display: flex;
  flex-wrap: wrap;
  background: ${({ theme }) => theme.colorDark};
  color: ${({ theme }) => theme.colorGray};
  padding: 0.5rem ${GUTTER};
  margin: 0.5rem 0 0;
  max-height: 8rem;
  overflow-y: auto;
  border-radius: ${GUTTER} ${GUTTER} 0 0;
  box-shadow: inset 0 10px 10px ${({ theme }) => rgba(theme.colorDark, 0.1)};
  list-style-type: none;
  text-shadow: ${({ theme }) => theme.textShadowWithDarkBg};

  ::-webkit-scrollbar-track {
    background-color: transparent;
  }

  ::-webkit-scrollbar {
    width: ${GUTTER};
    background-color: transparent;
  }

  ::-webkit-scrollbar-thumb {
    border-radius: ${GUTTER} 0 0 ${GUTTER};
    background-color: ${({ theme }) => rgba(theme.colorGray, 0.5)};
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

interface StyledPingResultProps {
  hasPing: boolean
}

const StyledPingResult = styled.div<StyledPingResultProps>`
  display: flex;
  align-items: center;
  background: ${({ theme }) => theme.colorDark};
  color: ${({ theme }) => theme.colorGray};
  border-radius: ${({ hasPing }) => (hasPing ? 0 : GUTTER)}
    ${({ hasPing }) => (hasPing ? 0 : GUTTER)} ${GUTTER} ${GUTTER};
  padding: calc(${GUTTER} / 2) ${GUTTER};
  border-top: 1px dashed ${({ theme }) => rgba(theme.colorGray, 0.1)};
  flex-wrap: wrap;
  justify-content: space-between;
`
const StyledPingResultTimes = styled.div``
const StyledPingResultAvg = styled.div``

@observer
export default class Ping extends Component {
  private pingTimer: number = 0
  private refItemContainer: RefObject<HTMLUListElement>

  public constructor(props) {
    super(props)

    this.refItemContainer = createRef()
  }

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
    const { appendPingItem } = store
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
            if (!this.refItemContainer.current) {
              return
            }

            const st = this.refItemContainer.current.scrollTop
            const sh = this.refItemContainer.current.scrollHeight

            if (st < sh) {
              this.refItemContainer.current.scrollTop = sh
            }
          }, 100)
        }
      })
      .catch(err => {})
  }

  private renderItems() {
    const { pingItemsCount, pingItems } = store

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
      <StyledPingItemContainer ref={this.refItemContainer}>
        {items}
      </StyledPingItemContainer>
    )
  }

  private renderResults() {
    const { pingItemsCount, pingItems } = store
    const timeItems = pingItems.map(({ time }) => time)
    const avg = pingItemsCount
      ? Math.floor(timeItems.reduce((a, b) => a + b, 0) / pingItemsCount)
      : 0
    const max = pingItemsCount ? Number(Math.max(...timeItems)) : 0
    const min = pingItemsCount ? Number(Math.min(...timeItems)) : 0

    return (
      <StyledPingResult hasPing={!!pingItemsCount}>
        <StyledPingResultTimes>
          {template(gettext('Times:${times}'), { times: pingItemsCount })}
        </StyledPingResultTimes>
        <StyledPingResultAvg>
          {template(gettext('Min:${min} / Max:${max} / Avg:${avg}'), {
            min,
            max,
            avg,
          })}
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
        <CardGrid name={this.pingBtn()} tablet={[1, 1]}>
          {this.renderItems()}
          {this.renderResults()}
        </CardGrid>
      </Row>
    )
  }
}
