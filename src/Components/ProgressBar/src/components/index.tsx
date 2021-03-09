import { GUTTER } from '@/Config/src'
import { formatBytes } from '@/Utils/src/components/format-bytes'
import { gradientColors } from '@/Utils/src/components/gradient'
import { rgba } from 'polished'
import React from 'react'
import styled from 'styled-components'
export interface ProgressBarProps {
  title?: string
  value: number
  max: number
  isCapacity: boolean
  percentTag?: string
  left?: string
}
const StyledProgressBar = styled.div`
  position: relative;
`
const StyledProgressOverview = styled.div`
  position: absolute;
  right: ${GUTTER};
  bottom: 0;
  z-index: 1;
  font-weight: 700;
  color: ${({ theme }) => theme['progress.fg']};
  line-height: 2rem;
  font-family: 'Arial Black';
  text-shadow: 0 1px 1px #000;
`
const StyledProgressPercent = styled(StyledProgressOverview)`
  left: ${GUTTER};
  right: auto;
`
const StyledProgressShell = styled.div`
  position: relative;
  display: flex;
  width: 100%;
  height: 2rem;
  background: ${({ theme }) => theme['progress.bg']};
  border-radius: ${GUTTER};
  padding: 0.3rem;
`
interface StyledProgressValueProps {
  percent: number
}
const StyledProgressValue = styled.div.attrs(
  ({ percent }: StyledProgressValueProps) => ({
    style: {
      backgroundColor: `#${
        gradientColors('#00cc00', '#ef2d2d')[~~percent - 1]
      }`,
      width: `${percent}%`,
    },
  })
)<StyledProgressValueProps>`
  position: relative;
  transition: width 0.5s;
  border-radius: ${GUTTER};
  background-color: ${({ theme }) => theme['progress.value.bg']};
  overflow: hidden;
  box-shadow: ${({ theme }) =>
    [
      theme.isDark ? `inset 0 0 0 10px ${rgba('#000', 0.75)}` : '',
      '0 0 1px 1px #000',
    ]
      .filter((n) => n)
      .join(',')};
  ::after,
  ::before {
    position: absolute;
    content: '';
    top: 0;
    height: 61.8%;
    width: 100%;
    border-radius: 0 0 50% 50%;
    background: ${({ theme }) => theme['progress.value.after.bg']};
  }
  ::before {
    background: ${({ theme }) => theme['progress.value.before.bg']};
    opacity: 1;
    height: 1px;
    border-radius: 0;
  }
`
export function ProgressBar({
  title = '',
  value,
  max,
  isCapacity,
  percentTag = '%',
  left = '',
}: ProgressBarProps) {
  const percent = max === 0 || value === 0 ? 0 : (value / max) * 100
  const overview = isCapacity
    ? `${formatBytes(value)} / ${formatBytes(max)}`
    : `${value.toFixed(1)}${percentTag} / ${max}${percentTag}`
  const overviewPercent = left ? left : `${percent.toFixed(1)}${percentTag}`
  return (
    <StyledProgressBar title={title}>
      <StyledProgressPercent>{overviewPercent}</StyledProgressPercent>
      <StyledProgressOverview>{overview}</StyledProgressOverview>
      <StyledProgressShell>
        <StyledProgressValue percent={percent} />
      </StyledProgressShell>
    </StyledProgressBar>
  )
}
