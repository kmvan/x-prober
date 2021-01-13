import CardGrid from '@/Card/src/components/card-grid'
import React, { useCallback, useRef } from 'react'
import Row from '@/Grid/src/components/row'
import serverFetch from '@/Fetch/src/server-fetch'
import store from '../stores'
import styled from 'styled-components'
import template from '@/Helper/src/components/template'
import { BORDER_RADIUS, GUTTER } from '@/Config/src'
import { device } from '@/Style/src/components/devices'
import { gettext } from '@/Language/src'
import { lighten, rgba } from 'polished'
import { observer } from 'mobx-react-lite'
import { OK } from '@/Restful/src/http-status'
const StyledPingBtn = styled.a`
  display: block;
  text-align: center;
  color: ${({ theme }) => theme.colorGray};
  background: ${({ theme }) => theme.colorDark};
  border-radius: ${BORDER_RADIUS};
  padding: calc(${GUTTER} / 2) ${GUTTER};
  text-shadow: ${({ theme }) => theme.textShadowWithDarkBg};
  margin-right: ${GUTTER};
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
  background: ${({ theme }) => lighten(0.01, theme.colorDark)};
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
  border-top: 1px solid ${({ theme }) => rgba(theme.colorGray, 0.1)};
  flex-wrap: wrap;
  justify-content: space-between;
`
const StyledPingResultTimes = styled.div``
const StyledPingResultAvg = styled.div``
const Items = observer(() => {
  const { pingItems } = store
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
  return <>{items}</>
})
const Results = observer(() => {
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
})
const Ping = observer(() => {
  const { pingItemsCount } = store
  let pingTimer: number = 0
  const refItemContainer = useRef<HTMLUListElement>(null)
  const onClickPing = useCallback(async () => {
    const { isPing, setIsPing } = store
    if (isPing) {
      setIsPing(false)
      clearTimeout(pingTimer)
      return
    }
    setIsPing(true)
    await pingLoop()
  }, [pingTimer])
  const pingLoop = useCallback(async () => {
    await ping()
    pingTimer = window.setTimeout(async () => {
      await pingLoop()
    }, 1000)
  }, [pingTimer])
  const ping = async () => {
    const { appendPingItem } = store
    const start = +new Date()
    const { data, status } = await serverFetch('ping')
    if (status === OK) {
      const { time } = data
      const end = +new Date()
      const serverTime = time * 1000
      appendPingItem({
        time: Math.floor(end - start - serverTime),
      })
      setTimeout(() => {
        if (!refItemContainer.current) {
          return
        }
        const st = refItemContainer.current.scrollTop
        const sh = refItemContainer.current.scrollHeight
        if (st < sh) {
          refItemContainer.current.scrollTop = sh
        }
      }, 100)
    }
  }
  return (
    <Row>
      <CardGrid
        name={
          <StyledPingBtn onClick={onClickPing}>
            {store.isPing ? gettext('⏸️ Stop ping') : gettext('👆 Start ping')}
          </StyledPingBtn>
        }
        tablet={[1, 1]}>
        {!!pingItemsCount && (
          <StyledPingItemContainer ref={refItemContainer}>
            <Items />
          </StyledPingItemContainer>
        )}
        <Results />
      </CardGrid>
    </Row>
  )
})
export default Ping
